<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Resources\DoctorResource;
use Tests\RequestFactories\AssignClinicsToDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

dataset(name: 'assign-clinics-validation-rules', dataset: [
    'clinics must be an array' => ['clinics', 'clinic-1', 'clinics'],
    'clinics must reference existing clinics' => ['clinics', [0], 'clinics'],
    'clinics items must be integers' => ['clinics', ['not-a-number'], 'clinics.0'],
]);

describe('doctors', function (): void {
    it(description: 'can assign clinics to a doctor successfully', closure: function (): void {
        $doctor = DoctorFactory::new()->createOne();

        $data = AssignClinicsToDoctorRequestFactory::new()->create();

        $response = postJson(url("/api/doctors/$doctor->id/clinics"), $data);

        $doctor->load('clinics');

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = DoctorResource::make($doctor)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertCreated()
            ->assertJsonPath('data', $expected);

        /** @var array $clinics */
        $clinics = $data['clinics'];

        assertDatabaseHas('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinics[0],
        ]);
    });

    it(description: 'replaces the previously assigned clinics', closure: function (): void {
        $previousClinic = ClinicFactory::new()->createOne();
        $doctor = DoctorFactory::new()
            ->recycle($previousClinic)
            ->createOne();

        $data = AssignClinicsToDoctorRequestFactory::new()->create();

        $response = postJson(url("/api/doctors/$doctor->id/clinics"), $data);

        $response->assertCreated();

        /** @var array $clinics */
        $clinics = $data['clinics'];

        assertDatabaseHas('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinics[0],
        ]);

        assertDatabaseMissing('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $previousClinic->id,
        ]);
    });

    it(description: 'detaches every clinic when none is provided', closure: function (): void {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->createOne();

        $response = postJson(url("/api/doctors/$doctor->id/clinics"), []);

        $response->assertCreated();

        expect($doctor->clinics()->count())->toBeEmpty();
    });

    it(
        'cannot assign clinics with invalid data',
        closure: function (string $field, string|array $value, string $errorField): void {
            $doctor = DoctorFactory::new()->createOne();

            $response = postJson(url("/api/doctors/$doctor->id/clinics"), [$field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('assign-clinics-validation-rules');
});
