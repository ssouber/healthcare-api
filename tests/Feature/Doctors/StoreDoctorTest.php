<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Controllers\StoreDoctorController;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Models\Doctor;
use Tests\RequestFactories\StoreDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
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
    'clinics must reference existing clinics' => ['clinics', [0], 'clinics.0'],
    'clinics items must be integers' => ['clinics', ['not-a-number'], 'clinics.0'],
]);

describe('doctors', function (): void {
    /** @see StoreDoctorController */
    it(description: 'can create a doctor with clinics successfully', closure: function (): void {
        $data = StoreDoctorRequestFactory::new()->create();

        $response = postJson(url('/api/doctors'), $data);

        $doctor = Doctor::query()
            ->where('name', $data['name'])
            ->firstOrFail();

        $doctor->load('clinics');

        $encoded = json_encode(DoctorResource::make($doctor)->resolve());
        assert(is_string($encoded));

        $expected = json_decode($encoded, true);
        assert(is_array($expected));

        $response
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json->whereAll($expected)
                )
            );

        assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => $data['name'],
        ]);

        $clinics = $data['clinics'];
        assert(is_array($clinics));

        assertDatabaseHas('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinics[0],
        ]);
    });

    it(description: 'can create a doctor without clinics', closure: function (): void {
        $data = StoreDoctorRequestFactory::new()->without('clinics')->create();

        $response = postJson(url('/api/doctors'), $data);

        $doctor = Doctor::query()
            ->where('name', $data['name'])
            ->firstOrFail();

        $response->assertCreated();

        assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => $data['name'],
        ]);

        expect($doctor->clinics()->count())->toBe(0);
    });

    it(
        'cannot create a doctor with invalid data',
        function (string $field, string|array $value, string $errorField): void {
            $data = StoreDoctorRequestFactory::new()->create();

            $response = postJson(url('/api/doctors'), [...$data, $field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('validation-rules');
});
