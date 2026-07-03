<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Controllers\ListDoctorController;
use function Pest\Laravel\getJson;

describe('doctors', function (): void {
    /** @see ListDoctorController */
    it(description: 'can list doctors with their clinics', closure: function (): void {
        DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->count(3)
            ->create();

        $response = getJson(url('/api/doctors'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has(
                        'data.0',
                        fn (AssertableJson $json): AssertableJson => $json
                            ->hasAll(['id', 'name', 'clinics'])
                            ->has('clinics', 2)
                            ->etc()
                    )
                    ->etc()
            );
    });

    it(description: 'returns an empty list when there are no doctors', closure: function (): void {
        $response = getJson(url('/api/doctors'));

        $response
            ->assertOk()
            ->assertJsonCount(0, 'data');
    });

    it(description: 'can filter doctors by name', closure: function (): void {
        DoctorFactory::new()->name('House')->createOne();
        DoctorFactory::new()->name('Wilson')->createOne();

        $response = getJson(url('/api/doctors') . '?filter[name]=House');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'House');
    });
});
