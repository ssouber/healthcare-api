<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Lightit\Clinics\App\Controllers\StoreClinicController;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Clinics\Domain\Models\Clinic;
use Tests\RequestFactories\StoreClinicRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

dataset('store-clinic-validation-rules', [
    'name is required' => ['name', '', 'name'],
    'name must be a string' => ['name', ['array'], 'name'],
    'name not too short' => ['name', 'ams', 'name'],
    'name not too long' => ['name', Str::repeat('longg', 60), 'name'],

    'address is required' => ['address', '', 'address'],
    'address must be a string' => ['address', ['array'], 'address'],
    'address not too long' => ['address', Str::repeat('longg', 60), 'address'],

    'doctors must be an array' => ['doctors', 'doctor-1', 'doctors'],
    'doctors must reference existing doctors' => ['doctors', [0], 'doctors'],
]);

describe('clinics', function (): void {
    /** @see StoreClinicController */
    it('can create a clinic with doctors successfully', function (): void {
        $data = StoreClinicRequestFactory::new()->create();

        $response = postJson(url('/api/clinics'), $data);

        $clinic = Clinic::query()
            ->where('name', $data['name'])
            ->firstOrFail()
            ->loadCount('doctors');

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = ClinicResource::make($clinic)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertCreated()
            ->assertJsonPath('data', $expected);

        assertDatabaseHas(Clinic::class, [
            'id' => $clinic->id,
            'name' => $data['name'],
            'address' => $data['address'],
        ]);

        /** @var array<int> $doctors */
        $doctors = $data['doctors'];

        assertDatabaseHas('clinic_doctor', [
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctors[0],
        ]);
    });

    it('can create a clinic without doctors', function (): void {
        $data = StoreClinicRequestFactory::new()->without('doctors')->create();

        $response = postJson(url('/api/clinics'), $data);

        $clinicId = $response->json('data.id');

        $response
            ->assertCreated()
            ->assertJsonPath('data.doctors_count', 0);

        assertDatabaseHas(Clinic::class, [
            'id' => $clinicId,
            'name' => $data['name'],
        ]);

        assertDatabaseMissing('clinic_doctor', [
            'clinic_id' => $clinicId,
        ]);
    });

    it('cannot create a clinic with an unexisting doctor', function (): void {
        $data = StoreClinicRequestFactory::new()->doctors([0])->create();

        $response = postJson(url('/api/clinics'), $data);

        $response->assertUnprocessable();

        assertDatabaseMissing(Clinic::class, [
            'name' => $data['name'],
        ]);
    });

    it(
        'cannot create a clinic with invalid data',
        function (string $field, string|array $value, string $errorField): void {
            $data = StoreClinicRequestFactory::new()->create();

            $response = postJson(url('/api/clinics'), [...$data, $field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('store-clinic-validation-rules');
});
