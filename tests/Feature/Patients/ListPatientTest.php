<?php

declare(strict_types=1);

use Database\Factories\PatientFactory;
use Lightit\Patients\App\Controllers\ListPatientController;
use Lightit\Patients\App\Resources\PatientResource;
use function Pest\Laravel\getJson;

describe('patients', function (): void {
    /** @see ListPatientController */
    it(description: 'can list patients', closure: function (): void {
        $patients = PatientFactory::new()
            ->createMany(4)
            ->sortByDesc('id');

        /** @var array{data: array} $expectedResponse */
        $expectedResponse = PatientResource::collection($patients)->response()->getData(true);

        $response = getJson(url('/api/patients'));

        $response
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data', $expectedResponse['data']);
    });

    it(description: 'returns an empty list when there are no patients', closure: function (): void {
        getJson(url('/api/patients'))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    });

    it(description: 'can filter patients by name', closure: function (): void {
        PatientFactory::new()->name('House')->createOne();
        PatientFactory::new()->name('Wilson')->createOne();

        getJson(url('/api/patients') . '?filter[name]=House')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'House');
    });

    it(description: 'can filter patients by email', closure: function (): void {
        PatientFactory::new()->email('house@example.com')->createOne();
        PatientFactory::new()->email('wilson@example.com')->createOne();

        getJson(url('/api/patients') . '?filter[email]=house@example.com')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'house@example.com');
    });
});
