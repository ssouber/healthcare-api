<?php

declare(strict_types=1);

use Database\Factories\PatientFactory;
use Lightit\Patients\App\Controllers\GetPatientController;
use Lightit\Patients\App\Resources\PatientResource;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;

describe('patients', function (): void {
    /** @see GetPatientController */
    it('can get a patient successfully', function (): void {
        $patient = PatientFactory::new()->createOne();

        /** @var array{data: array} $expected */
        $expected = PatientResource::make($patient)->response()->getData(true);

        getJson(url("/api/patients/$patient->id"))
            ->assertOk()
            ->assertJsonPath('data', $expected['data']);
    });

    it('returns not found when the patient does not exist', function (): void {
        getJson(url('/api/patients/999999'))
            ->assertNotFound();
    });

    it('returns not found when the patient was deleted', function (): void {
        $patient = PatientFactory::new()->createOne();

        deleteJson(url("/api/patients/$patient->id"));

        getJson(url("/api/patients/$patient->id"))
            ->assertNotFound();
    });
});
