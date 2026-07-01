<?php

use Database\Factories\DoctorFactory;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Models\Doctor;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

function getLongName(): string
{
    return Str::repeat(string: 'longg', times: random_int(min: 55, max: 60));
}

dataset(name: 'validation-rules', dataset: [
    'name is required' => ['name', ''],
    'name be a string' => ['name', ['array']],
    'name not too short' => ['name', 'ams'],
    'name not too long' => ['name', getLongName()],
]);

describe('doctors', function (): void {
    /** @see StoreDoctorController */
    it(description: 'can create a doctor successfully', closure: function (): void {
        $data = DoctorFactory::new()->make()->toArray();

        $response = postJson(url('/api/doctors'), $data);

        $doctor = Doctor::query()
            ->where('name', $data['name'])
            ->firstOrFail();

        $doctor->load('clinics');

        $expected = json_decode(json_encode(DoctorResource::make($doctor)->resolve()), true);
        assert(is_array($expected));

        $response
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json->whereAll($expected)
            );

        assertDatabaseHas('doctor', [
            'name' => $data['name'],
            'clinics' => $data['clinics'],
        ]);
    });

    it('cannot create a user with invalid data', closure: function (string $field, string|array $value): void {
        $data = DoctorFactory::new()->make()->toArray();

        $response = postJson(url('/api/doctors'), [...$data, $field => $value]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([$field], 'error.fields');
    })->with('validation-rules');
});
