<?php

declare(strict_types=1);

use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Controllers\DeleteDoctorController;
use Lightit\Doctors\Domain\Models\Doctor;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

describe('doctors', function (): void {
    /** @see DeleteDoctorController */
    it('can delete a doctor successfully', function (): void {
        $doctor = DoctorFactory::new()->createOne();

        $response = deleteJson(url("/api/doctors/$doctor->id"));

        $response->assertNoContent();

        assertSoftDeleted(Doctor::class, [
            'id' => $doctor->id,
        ]);
    });

    it('returns not found when the doctor does not exist', function (): void {
        $response = deleteJson(url('/api/doctors/999999'));

        $response->assertNotFound();
    });

    it('returns not found when the doctor was soft deleted', function (): void {
        $doctor = DoctorFactory::new()->createOne();
        deleteJson(url("/api/doctors/$doctor->id"));

        $response = deleteJson(url("/api/doctors/$doctor->id"));

        $response->assertNotFound();
    });
});
