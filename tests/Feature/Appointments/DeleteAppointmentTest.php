<?php

declare(strict_types=1);

use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\Domain\Models\Appointment;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\assertSoftDeleted;

describe('appointments', function (): void {
    /** @see DeleteAppointmentController */
    it('delete the authenticated patient appointment', function (): void {
        $patient = PatientFactory::new()->createOne();

        $appointment = AppointmentFactory::new()
            ->forPatient($patient)
            ->createOne()
            ->load('clinic', 'doctor', 'patient');

        actingAs($patient)
            ->deleteJson(url("api/appointments/$appointment->id"))
            ->assertNoContent();

        assertSoftDeleted(Appointment::class, [
            'id' => $appointment->id,
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
            ->createOne()
            ->load('clinic', 'doctor', 'patient');

        actingAs($patient)
            ->deleteJson(url("api/appointments/$appointment->id"));

        actingAs($patient)
            ->deleteJson(url("api/appointments/$appointment->id"))
            ->assertNotFound();

    });
});
