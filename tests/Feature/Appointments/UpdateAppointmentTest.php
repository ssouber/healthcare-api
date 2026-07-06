<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;
use Tests\RequestFactories\UpsertAppointmentRequestFactory;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

dataset(name: 'validation-rules', dataset: [
    'doctor is required' => ['doctor', '', 'doctor'],
    'doctor must be an integer' => ['doctor', 'not-a-number', 'doctor'],
    'doctor must reference an existing doctor' => ['doctor', 0, 'doctor'],

    'clinic is required' => ['clinic', '', 'clinic'],
    'clinic must be an integer' => ['clinic', 'not-a-number', 'clinic'],
    'clinic must reference an existing clinic' => ['clinic', 0, 'clinic'],

    'starts_at is required' => ['starts_at', '', 'starts_at'],
    'starts_at must be a valid datetime' => ['starts_at', 'not-a-date', 'starts_at'],
    'starts_at must match the Y-m-d H:i format' => ['starts_at', '2026-12-31', 'starts_at'],
    'starts_at must be a future datetime' => ['starts_at', '2000-01-01 10:00', 'starts_at'],
]);

describe('appointments', function (): void {
    /** @see UpdateAppointmentController */
    it('updates an appointment', function (): void {
        $patient = PatientFactory::new()->createOne();
        $appointment = AppointmentFactory::new()
            ->forPatient($patient)
            ->startsAt(CarbonImmutable::now()->addHours(24))
            ->createOne();

        $data = UpsertAppointmentRequestFactory::new()
            ->startsAt(CarbonImmutable::now()->addHours(48))
            ->create();

        $response = actingAs($patient)->putJson(url("api/appointments/$appointment->id"), $data);

        $appointment = Appointment::query()
            ->where('id', $response->json('data.id'))
            ->with(['patient', 'clinic', 'doctor'])
            ->firstOrFail();

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = AppointmentResource::make($appointment)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertSuccessful()
            ->assertJsonPath('data', $expected);

        assertDatabaseHas(Appointment::class, [
            'id' => $appointment->id,
            'doctor_id' => $data['doctor'],
            'clinic_id' => $data['clinic'],
            'patient_id' => $patient->id,
            'starts_at' => $data['starts_at'],
            'status' => AppointmentStatus::SCHEDULED->value,
        ]);
    });

    it('returns unauthorized', function (): void {
        $appointment = AppointmentFactory::new()->startsAt(CarbonImmutable::now()->addHours(24))->createOne();
        $data = UpsertAppointmentRequestFactory::new()->create();

        putJson(url("api/appointments/$appointment->id"), $data)
            ->assertUnauthorized();
    });

    it(
        description: 'cannot update an appointment with invalid data',
        closure: function (string $field, string|int $value, string $errorField): void {
            $patient = PatientFactory::new()->createOne();
            $appointment = AppointmentFactory::new()
                ->forPatient($patient)
                ->startsAt(CarbonImmutable::now()->addHours(24))
                ->createOne();
            $data = UpsertAppointmentRequestFactory::new()->create();

            actingAs($patient)
                ->putJson(url("api/appointments/$appointment->id"), [...$data, $field => $value])
                ->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('validation-rules');
});
