<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Doctors\Domain\Models\Doctor;
use Tests\RequestFactories\UpsertAppointmentRequestFactory;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

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
    /** @see StoreAppointmentController */
    it('stores an appointment', function (): void {
        $patient = PatientFactory::new()->createOne();

        $data = UpsertAppointmentRequestFactory::new()->create();

        $response = actingAs($patient)->postJson(url('api/appointments'), $data);

        $appointment = Appointment::query()
            ->where('id', $response->json('data.id'))
            ->with(['patient', 'clinic', 'doctor'])
            ->firstOrFail();

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = AppointmentResource::make($appointment)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertCreated()
            ->assertJsonPath('data', $expected);

        assertDatabaseHas(Appointment::class, [
            'id' => $appointment->id,
        ]);
    });

    it('returns unauthorized', function (): void {
        $data = UpsertAppointmentRequestFactory::new()->create();

        postJson(url('api/appointments'), $data)
            ->assertUnauthorized();
    });

    it('cannot create an appointment when the doctor is already booked', function (): void {
        $patient = PatientFactory::new()->createOne();
        $startsAt = CarbonImmutable::now()->addDays(3);

        $data = UpsertAppointmentRequestFactory::new()->startsAt($startsAt)->create();

        /** @var Doctor $doctor */
        $doctor = Doctor::query()->findOrFail($data['doctor']);

        AppointmentFactory::new()
            ->forDoctor($doctor)
            ->startsAt($startsAt)
            ->createOne();

        actingAs($patient)
            ->postJson(url('api/appointments'), $data)
            ->assertConflict()
            ->assertJsonPath('error.code', 'doctor_not_available');
    });

    it('cannot create an appointment when the patient is already booked', function (): void {
        $patient = PatientFactory::new()->createOne();
        $startsAt = CarbonImmutable::now()->addDays(3);

        $data = UpsertAppointmentRequestFactory::new()->startsAt($startsAt)->create();

        AppointmentFactory::new()
            ->forPatient($patient)
            ->startsAt($startsAt)
            ->createOne();

        actingAs($patient)
            ->postJson(url('api/appointments'), $data)
            ->assertConflict()
            ->assertJsonPath('error.code', 'patient_not_available');
    });

    it(
        description: 'cannot create an appointment with invalid data',
        closure: function (string $field, string|int $value, string $errorField): void {
            $patient = PatientFactory::new()->createOne();
            $data = UpsertAppointmentRequestFactory::new()->create();

            actingAs($patient)
                ->postJson(url('api/appointments'), [...$data, $field => $value])
                ->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('validation-rules');
});
