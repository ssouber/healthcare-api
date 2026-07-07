<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Resources\DoctorResource;
use Tests\RequestFactories\AssignClinicsToDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

dataset('assign-clinics-validation-rules', [
    'clinics must be an array' => ['clinics', 'clinic-1', 'clinics'],
    'clinics must reference existing clinics' => ['clinics', [0], 'clinics'],
    'clinics items must be integers' => ['clinics', ['not-a-number'], 'clinics.0'],
]);

describe('doctors', function (): void {
    it('can assign clinics to a doctor successfully', function (): void {
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

    it('replaces the previously assigned clinics', function (): void {
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

    it('detaches every clinic when none is provided', function (): void {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->createOne();

        $response = postJson(url("/api/doctors/$doctor->id/clinics"), []);

        $response->assertCreated();

        expect($doctor->clinics()->count())->toBeEmpty();
    });

    it(
        'cannot assign clinics with invalid data',
        function (string $field, string|array $value, string $errorField): void {
            $doctor = DoctorFactory::new()->createOne();

            $response = postJson(url("/api/doctors/$doctor->id/clinics"), [$field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('assign-clinics-validation-rules');
});
