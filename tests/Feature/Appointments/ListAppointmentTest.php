<?php

declare(strict_types=1);

use Database\Factories\AppointmentFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\App\Resources\AppointmentResource;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

describe('appointments', function (): void {
    /** @see ListAppointmentController */
    it('lists the authenticated patient appointments', function (): void {
        $patient = PatientFactory::new()->createOne();

        $appointments = AppointmentFactory::new()
            ->forPatient($patient)
            ->createMany(3)
            ->load('clinic', 'doctor', 'patient')
            ->sortByDesc('starts_at');

        /** @var array{data: array} $expectedResponse */
        $expectedResponse = AppointmentResource::collection($appointments)
            ->response()
            ->getData(true);

        actingAs($patient)
            ->getJson(url('api/appointments'))
            ->assertSuccessful()
            ->assertJsonPath('data', $expectedResponse['data']);
    });

    it('returns and empty array', function (): void {
        $patient = PatientFactory::new()->createOne();

        actingAs($patient)
            ->getJson(url('api/appointments'))
            ->assertSuccessful()
            ->assertJsonCount(0, 'data');
    });

    it('returns unauthorized error when patient is no authenticated', function (): void {
        AppointmentFactory::new()
            ->createMany(3)
            ->load('clinic', 'doctor', 'patient')
            ->sortByDesc('starts_at');

        getJson(url('api/appointments'))
            ->assertUnauthorized();
    });

    it('return unauthorized error when patient is no authenticated', function (): void {
        AppointmentFactory::new()
            ->createMany(3)
            ->load('clinic', 'doctor', 'patient')
            ->sortByDesc('starts_at');

        getJson(url('api/appointments'))
            ->assertUnauthorized();
    });
});
