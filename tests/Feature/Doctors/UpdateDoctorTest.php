<?php

declare(strict_types=1);

use Database\Factories\DoctorFactory;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Controllers\StoreDoctorController;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Models\Doctor;
use Tests\RequestFactories\UpdateDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

dataset(name: 'update-validation-rules', dataset: [
    'name is required' => ['name', '', 'name'],
    'name be a string' => ['name', ['array'], 'name'],
    'name not too short' => ['name', 'ams', 'name'],
    'name not too long' => ['name', Str::repeat(string: 'longg', times: 60), 'name'],
]);

describe('doctors', function (): void {
    /** @see StoreDoctorController */
    it(description: 'can update a doctor successfully', closure: function (): void {
        $existingDoctor = DoctorFactory::new()->createOne([
            'name' => 'old',
        ]);

        $data = UpdateDoctorRequestFactory::new()->create([
            'name' => 'new name',
        ]);

        $response = putJson(url("/api/doctors/$existingDoctor->id"), $data);

        $updatedDoctor = Doctor::query()
            ->where('name', $data['name'])
            ->firstOrFail();

        $updatedDoctor->load('clinics');

        $encoded = json_encode(DoctorResource::make($updatedDoctor)->resolve());
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

        assertDatabaseHas('doctors', [
            'id' => $updatedDoctor->id,
            'name' => $data['name'],
        ]);
    });

    it(
        'cannot update a doctor with invalid data',
        closure: function (string $field, string|array $value, string $errorField): void {
            $existingDoctor = DoctorFactory::new()->createOne();

            $data = UpdateDoctorRequestFactory::new()->create();

            $response = putJson(url("/api/doctors/$existingDoctor->id"), [...$data, $field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('update-validation-rules');
});
