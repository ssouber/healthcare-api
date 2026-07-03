<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Controllers\GetDoctorController;
use Lightit\Doctors\App\Resources\DoctorResource;
use function Pest\Laravel\getJson;

describe('doctors', function (): void {
    /** @see GetDoctorController */
    it(description: 'can get a doctor with clinics successfully', closure: function (): void {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->createOne();

        $response = getJson(url("/api/doctors/$doctor->id"));

        $doctor->load('clinics');

        $encoded = json_encode(DoctorResource::make($doctor)->resolve());
        assert(is_string($encoded));

        $expected = json_decode($encoded, true);
        assert(is_array($expected));

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json->whereAll($expected)
                )
            );
    });

    it(description: 'returns not found when the doctor does not exist', closure: function (): void {
        $response = getJson(url('/api/doctors/999999'));

        $response->assertNotFound();
    });
});
