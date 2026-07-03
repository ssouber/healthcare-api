<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Clinics\App\Controllers\GetClinicController;
use Lightit\Clinics\App\Resources\ClinicResource;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;

describe('clinics', function (): void {
    /** @see GetClinicController */
    it(description: 'can get a clinic with doctors successfully', closure: function (): void {
        $clinic = ClinicFactory::new()
            ->hasDoctors(DoctorFactory::new()->count(2))
            ->createOne()
            ->loadCount('doctors');

        /** @var array{data: array} $expected */
        $expected = ClinicResource::make($clinic)->response()->getData(true);

        getJson(url("/api/clinics/$clinic->id"))
            ->assertOk()
            ->assertJsonPath('data', $expected['data']);
    });

    it(description: 'returns not found when the clinic does not exist', closure: function (): void {
        getJson(url('/api/clinics/999999'))
            ->assertNotFound();
    });

    it(description: 'returns not found when the clinic was deleted', closure: function (): void {
        $clinic = ClinicFactory::new()->createOne();

        deleteJson(url("/api/clinics/$clinic->id"));

        getJson(url("/api/clinics/$clinic->id"))
            ->assertNotFound();
    });
});
