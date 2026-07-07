<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Lightit\Clinics\App\Controllers\DeleteClinicController;
use Lightit\Clinics\Domain\Models\Clinic;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

describe('clinics', function (): void {
    /** @see DeleteClinicController */
    it('can delete a clinic successfully', function (): void {
        $clinic = ClinicFactory::new()->createOne();

        $response = deleteJson(url("/api/clinics/$clinic->id"));

        $response->assertNoContent();

        assertSoftDeleted(Clinic::class, [
            'id' => $clinic->id,
        ]);
    });

    it('returns not found when the clinic does not exist', function (): void {
        $response = deleteJson(url('/api/clinics/999999'));

        $response->assertNotFound();
    });

    it('returns not found when the clinic was soft deleted', function (): void {
        $clinic = ClinicFactory::new()->createOne();
        deleteJson(url("/api/clinics/$clinic->id"));

        $response = deleteJson(url("/api/clinics/$clinic->id"));

        $response->assertNotFound();
    });
});
