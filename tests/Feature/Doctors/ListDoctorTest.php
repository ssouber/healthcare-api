<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Doctors\App\Controllers\ListDoctorController;
use Lightit\Doctors\App\Resources\DoctorResource;
use function Pest\Laravel\getJson;

describe('doctors', function (): void {
    /** @see ListDoctorController */
    it('can list doctors with their clinics', function (): void {
        $doctors = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->createMany(4)
            ->load('clinics')
            ->sortByDesc('id');

        /** @var array{data: array} $expectedResponse */
        $expectedResponse = DoctorResource::collection($doctors)->response()->getData(true);

        $response = getJson(url('/api/doctors'));

        $response
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data', $expectedResponse['data']);
    });

    it('returns an empty list when there are no doctors', function (): void {
        getJson(url('/api/doctors'))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    });

    it('can filter doctors by name', function (): void {
        DoctorFactory::new()->name('House')->createOne();
        DoctorFactory::new()->name('Wilson')->createOne();

        getJson(url('/api/doctors') . '?filter[name]=House')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'House');
    });
});
