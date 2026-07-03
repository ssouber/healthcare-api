<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Controllers\GetDoctorController;
use Lightit\Doctors\App\Resources\DoctorResource;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;

describe('doctors', function (): void {
    /** @see GetDoctorController */
    it(description: 'can get a doctor with clinics successfully', closure: function (): void {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->createOne();

        $doctor->load('clinics');

        /** @var array{data: array} $expected */
        $expected = DoctorResource::make($doctor)->response()->getData(true);

        getJson(url("/api/doctors/$doctor->id"))
            ->assertOk()
                ->assertJsonPath('data', $expected['data']);
    });

    it(description: 'returns not found when the doctor does not exist', closure: function (): void {
        getJson(url('/api/doctors/999999'))
            ->assertNotFound();
    });

    it(description: 'returns not found when the doctor was deleted', closure: function (): void {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->createOne();

        deleteJson(url("/api/doctors/$doctor->id"));

        getJson(url("/api/doctors/$doctor->id"))
            ->assertNotFound();
    });
});
