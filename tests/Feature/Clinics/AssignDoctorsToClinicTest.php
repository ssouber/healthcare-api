<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Clinics\App\Controllers\AssignDoctorsToClinicController;
use Lightit\Clinics\App\Resources\ClinicResource;
use Tests\RequestFactories\AssignDoctorsToClinicRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

dataset(name: 'assign-doctors-validation-rules', dataset: [
    'doctors must be an array' => ['doctors', 'doctor-1', 'doctors'],
    'doctors must reference existing doctors' => ['doctors', [0], 'doctors'],
]);

describe('clinics', function (): void {
    /** @see AssignDoctorsToClinicController */
    it(description: 'can assign doctors to a clinic successfully', closure: function (): void {
        $clinic = ClinicFactory::new()->createOne();

        $data = AssignDoctorsToClinicRequestFactory::new()->create();

        $response = postJson(url("/api/clinics/$clinic->id/doctors"), $data);

        $clinic->loadCount('doctors');

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = ClinicResource::make($clinic)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertCreated()
            ->assertJsonPath('data', $expected);

        /** @var array<int> $doctors */
        $doctors = $data['doctors'];

        assertDatabaseHas('clinic_doctor', [
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctors[0],
        ]);
    });

    it(description: 'replaces the previously assigned doctors', closure: function (): void {
        $clinic = ClinicFactory::new()
            ->hasDoctors(DoctorFactory::new())
            ->createOne();

        $previousDoctorId = $clinic->doctors()->firstOrFail()->id;

        $data = AssignDoctorsToClinicRequestFactory::new()->create();

        $response = postJson(url("/api/clinics/$clinic->id/doctors"), $data);

        $response->assertCreated();

        /** @var array<int> $doctors */
        $doctors = $data['doctors'];

        assertDatabaseHas('clinic_doctor', [
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctors[0],
        ]);

        assertDatabaseMissing('clinic_doctor', [
            'clinic_id' => $clinic->id,
            'doctor_id' => $previousDoctorId,
        ]);
    });

    it(description: 'detaches every doctor when none is provided', closure: function (): void {
        $clinic = ClinicFactory::new()
            ->hasDoctors(DoctorFactory::new()->count(2))
            ->createOne();

        $response = postJson(url("/api/clinics/$clinic->id/doctors"), []);

        $response->assertCreated();

        expect($clinic->doctors()->count())->toBe(0);
    });

    it(
        'cannot assign doctors with invalid data',
        closure: function (string $field, string|array $value, string $errorField): void {
            $clinic = ClinicFactory::new()->createOne();

            $response = postJson(url("/api/clinics/$clinic->id/doctors"), [$field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('assign-doctors-validation-rules');
});
