<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Illuminate\Support\Str;
use Lightit\Clinics\App\Controllers\UpdateClinicController;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Clinics\Domain\Models\Clinic;
use Tests\RequestFactories\UpdateClinicRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

dataset('update-clinic-validation-rules', [
    'name is required' => ['name', '', 'name'],
    'name must be a string' => ['name', ['array'], 'name'],
    'name not too short' => ['name', 'ams', 'name'],
    'name not too long' => ['name', Str::repeat('longg', 60), 'name'],

    'address is required' => ['address', '', 'address'],
    'address must be a string' => ['address', ['array'], 'address'],
    'address not too long' => ['address', Str::repeat('longg', 60), 'address'],
]);

describe('clinics', function (): void {
    /** @see UpdateClinicController */
    it('can update a clinic successfully', function (): void {
        $existingClinic = ClinicFactory::new()->name('old name')->createOne();

        $data = UpdateClinicRequestFactory::new()->name('new name')->create();

        $response = putJson(url("/api/clinics/$existingClinic->id"), $data);

        $updatedClinic = Clinic::query()
            ->where('name', $data['name'])
            ->firstOrFail()
            ->loadCount('doctors');

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = ClinicResource::make($updatedClinic)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertOk()
            ->assertJsonPath('data', $expected);

        assertDatabaseHas(Clinic::class, [
            'id' => $updatedClinic->id,
            'name' => $data['name'],
            'address' => $data['address'],
        ]);
    });

    it('returns not found when the clinic does not exist', function (): void {
        $data = UpdateClinicRequestFactory::new()->create();

        putJson(url('/api/clinics/999999'), $data)
            ->assertNotFound();
    });

    it(
        'cannot update a clinic with invalid data',
        function (string $field, string|array $value, string $errorField): void {
            $existingClinic = ClinicFactory::new()->createOne();

            $data = UpdateClinicRequestFactory::new()->create();

            $response = putJson(url("/api/clinics/$existingClinic->id"), [...$data, $field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('update-clinic-validation-rules');
});
