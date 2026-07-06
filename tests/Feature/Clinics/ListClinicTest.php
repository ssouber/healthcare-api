<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Clinics\App\Controllers\ListClinicController;
use Lightit\Clinics\App\Resources\ClinicResource;
use function Pest\Laravel\getJson;

describe('clinics', function (): void {
    /** @see ListClinicController */
    it('can list clinics with their doctors count', function (): void {
        $clinics = ClinicFactory::new()
            ->hasDoctors(DoctorFactory::new()->count(2))
            ->createMany(4)
            ->loadCount('doctors')
            ->sortByDesc('id');

        /** @var array{data: array} $expectedResponse */
        $expectedResponse = ClinicResource::collection($clinics)->response()->getData(true);

        $response = getJson(url('/api/clinics'));

        $response
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data', $expectedResponse['data']);
    });

    it('returns an empty list when there are no clinics', function (): void {
        getJson(url('/api/clinics'))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    });

    it('can filter clinics by name', function (): void {
        ClinicFactory::new()->name('Mayo')->createOne();
        ClinicFactory::new()->name('Cleveland')->createOne();

        getJson(url('/api/clinics') . '?filter[name]=Mayo')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mayo');
    });
});
