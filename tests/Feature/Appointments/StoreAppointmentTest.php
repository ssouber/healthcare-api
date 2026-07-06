<?php

declare(strict_types=1);

use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Models\Appointment;
use Tests\RequestFactories\StoreAppointmentRequestFactory;
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

        $data = StoreAppointmentRequestFactory::new()->create();

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
        $data = StoreAppointmentRequestFactory::new()->create();

        postJson(url('api/appointments'), $data)
            ->assertUnauthorized();
    });

    it(
        description: 'cannot create an appointment with invalid data',
        closure: function (string $field, string|int $value, string $errorField): void {
            $patient = PatientFactory::new()->createOne();
            $data = StoreAppointmentRequestFactory::new()->create();

            actingAs($patient)
                ->postJson(url('api/appointments'), [...$data, $field => $value])
                ->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('validation-rules');
});
