<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Lightit\Doctors\App\Controllers\StoreDoctorController;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Models\Doctor;
use Tests\RequestFactories\StoreDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

function getLongName(): string
{
    return Str::repeat(string: 'longg', times: random_int(min: 55, max: 60));
}

dataset(name: 'validation-rules', dataset: [
    'name is required' => ['name', '', 'name'],
    'name be a string' => ['name', ['array'], 'name'],
    'name not too short' => ['name', 'ams', 'name'],
    'name not too long' => ['name', getLongName(), 'name'],

    'clinics must be an array' => ['clinics', 'clinic-1', 'clinics'],
    'clinics must reference existing clinics' => ['clinics', [0], 'clinics'],
    'clinics items must be integers' => ['clinics', ['not-a-number'], 'clinics.0'],
]);

describe('doctors', function (): void {
    /** @see StoreDoctorController */
    it(description: 'can create a doctor with clinics successfully', closure: function (): void {
        $data = StoreDoctorRequestFactory::new()->create();

        $response = postJson(url('/api/doctors'), $data);

        $doctor = Doctor::query()
            ->where('name', $data['name'])
            ->with('clinics')
            ->firstOrFail();

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = DoctorResource::make($doctor)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertCreated()
            ->assertJsonPath('data', $expected);

        assertDatabaseHas(Doctor::class, [
            'id' => $doctor->id,
            'name' => $data['name'],
        ]);

        /** @var array<int> $clinics */
        $clinics = $data['clinics'];

        assertDatabaseHas('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinics[0],
        ]);
    });

    it(description: 'can create a doctor without clinics', closure: function (): void {
        $data = StoreDoctorRequestFactory::new()->without('clinics')->create();

        $response = postJson(url('/api/doctors'), $data);

        $doctorId = $response->json('data.id');

        $response->assertCreated();

        assertDatabaseHas(Doctor::class, [
            'id' => $doctorId,
            'name' => $data['name'],
        ]);

        /** @var array<int, mixed> $clinics */
        $clinics = $response->json('data.clinics');
        expect($clinics)->toBeEmpty();

        assertDatabaseMissing('clinic_doctor', [
            'doctor_id' => $doctorId,
        ]);
    });

    it(description: 'cannot create a doctor with unexisting clinic', closure: function (): void {
        $data = StoreDoctorRequestFactory::new()->state(['clinics' => [0]])->create();

        $response = postJson(url('/api/doctors'), $data);

        $response->assertUnprocessable();

        assertDatabaseMissing(Doctor::class, [
            'name' => $data['name'],
        ]);
    });

    it(
        'cannot create a doctor with invalid data',
        closure: function (string $field, string|array $value, string $errorField): void {
            $data = StoreDoctorRequestFactory::new()->create();

            $response = postJson(url('/api/doctors'), [...$data, $field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('validation-rules');
});
