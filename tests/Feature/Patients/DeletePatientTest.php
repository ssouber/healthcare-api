<?php

declare(strict_types=1);

use Database\Factories\PatientFactory;
use Lightit\Patients\App\Controllers\DeletePatientController;
use Lightit\Patients\Domain\Models\Patient;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

describe('patients', function (): void {
    /** @see DeletePatientController */
    it('can delete a patient successfully', function (): void {
        $patient = PatientFactory::new()->createOne();

        $response = deleteJson(url("/api/patients/$patient->id"));

        $response->assertNoContent();

        assertSoftDeleted(Patient::class, [
            'id' => $patient->id,
        ]);
    });

    it('returns not found when the patient does not exist', function (): void {
        $response = deleteJson(url('/api/patients/999999'));

        $response->assertNotFound();
    });

    it('returns not found when the patient was soft deleted', function (): void {
        $patient = PatientFactory::new()->createOne();
        deleteJson(url("/api/patients/$patient->id"));

        $response = deleteJson(url("/api/patients/$patient->id"));

        $response->assertNotFound();
    });
});
