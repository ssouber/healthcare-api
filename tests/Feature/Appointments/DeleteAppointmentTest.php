<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

describe('appointments', function (): void {
    /** @see DeleteAppointmentController */
    it('soft deletes the appointment when it starts more than 48 hours from now', function (): void {
        $patient = PatientFactory::new()->createOne();

        $appointment = AppointmentFactory::new()
            ->forPatient($patient)
            ->startsAt(CarbonImmutable::now()->addHours(72))
            ->createOne();

        actingAs($patient)
            ->deleteJson(url("api/appointments/$appointment->id"))
            ->assertNoContent();

        assertSoftDeleted(Appointment::class, [
            'id' => $appointment->id,
            'status' => AppointmentStatus::SCHEDULED->value,
        ]);
    });

    it('cancels the appointment when it starts within the next 48 hours', function (): void {
        $patient = PatientFactory::new()->createOne();

        $appointment = AppointmentFactory::new()
            ->forPatient($patient)
            ->startsAt(CarbonImmutable::now()->addHours(24))
            ->createOne();

        actingAs($patient)
            ->deleteJson(url("api/appointments/$appointment->id"))
            ->assertNoContent();


        assertDatabaseHas(Appointment::class, [
            'id' => $appointment->id,
            'status' => AppointmentStatus::CANCELLED->value,
            'deleted_at' => null,
        ]);
    });

    it('returns unauthorized with no authenticated patient', function (): void {
        $patient = PatientFactory::new()->createOne();

        $appointment = AppointmentFactory::new()
            ->forPatient($patient)
            ->createOne()
            ->load('clinic', 'doctor', 'patient');

        deleteJson(url("api/appointments/$appointment->id"))
            ->assertUnauthorized();
    });


    it('returns forbidden when trying to delete appointment from a patient that is not the current', function (): void {
        $patientA = PatientFactory::new()->createOne();
        $patientB = PatientFactory::new()->createOne();

        $appointment = AppointmentFactory::new()
            ->forPatient($patientA)
            ->createOne()
            ->load('clinic', 'doctor', 'patient');

        actingAs($patientB)
            ->deleteJson(url("api/appointments/$appointment->id"))
            ->assertForbidden();
    });

    it('returns not found when the appointment was soft deleted', function (): void {
        $patient = PatientFactory::new()->createOne();

        $appointment = AppointmentFactory::new()
            ->forPatient($patient)
            ->startsAt(CarbonImmutable::now()->addHours(72))
            ->createOne();

        actingAs($patient)
            ->deleteJson(url("api/appointments/$appointment->id"));

        actingAs($patient)
            ->deleteJson(url("api/appointments/$appointment->id"))
            ->assertNotFound();
    });
});
