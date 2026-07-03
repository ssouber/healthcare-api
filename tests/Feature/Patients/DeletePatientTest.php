<?php

declare(strict_types=1);

use Database\Factories\PatientFactory;
use Lightit\Patients\App\Controllers\DeletePatientController;
use Lightit\Patients\Domain\Models\Patient;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

describe('patients', function (): void {
    /** @see DeletePatientController */
    it(description: 'can delete a patient successfully', closure: function (): void {
        $patient = PatientFactory::new()->createOne();

        $response = deleteJson(url("/api/patients/$patient->id"));

        $response->assertNoContent();

        assertSoftDeleted(Patient::class, [
            'id' => $patient->id,
        ]);
    });

    it(description: 'returns not found when the patient does not exist', closure: function (): void {
        $response = deleteJson(url('/api/patients/999999'));

        $response->assertNotFound();
    });

    it(description: 'returns not found when the patient was soft deleted', closure: function (): void {
        $patient = PatientFactory::new()->createOne();
        deleteJson(url("/api/patients/$patient->id"));

        $response = deleteJson(url("/api/patients/$patient->id"));

        $response->assertNotFound();
    });
});
