<?php

declare(strict_types=1);

use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Controllers\DeleteDoctorController;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

describe('doctors', function (): void {
    /** @see DeleteDoctorController */
    it(description: 'can delete a doctor successfully', closure: function (): void {
        $doctor = DoctorFactory::new()->createOne();

        $response = deleteJson(url("/api/doctors/$doctor->id"));

        $response->assertNoContent();

        assertSoftDeleted('doctors', [
            'id' => $doctor->id,
        ]);
    });

    it(description: 'returns not found when the doctor does not exist', closure: function (): void {
        $response = deleteJson(url('/api/doctors/999999'));

        $response->assertNotFound();
    });
});
