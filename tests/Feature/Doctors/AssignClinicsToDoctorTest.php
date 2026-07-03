<?php

declare(strict_types=1);

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Illuminate\Testing\Fluent\AssertableJson;
use Lightit\Doctors\App\Resources\DoctorResource;
use Tests\RequestFactories\AssignClinicsToDoctorRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

dataset(name: 'assign-clinics-validation-rules', dataset: [
    'clinics must be an array' => ['clinics', 'clinic-1', 'clinics'],
    'clinics must reference existing clinics' => ['clinics', [0], 'clinics'],
    'clinics items must be integers' => ['clinics', ['not-a-number'], 'clinics.0'],
]);

describe('doctors', function (): void {
    it(description: 'can assign clinics to a doctor successfully', closure: function (): void {
        $doctor = DoctorFactory::new()->createOne();

        $data = AssignClinicsToDoctorRequestFactory::new()->create();

        $response = postJson(url("/api/doctors/$doctor->id/clinics"), $data);

        $doctor->load('clinics');

        $encoded = json_encode(DoctorResource::make($doctor)->resolve());
        assert(is_string($encoded));

        $expected = json_decode($encoded, true);
        assert(is_array($expected));

        $response
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json->whereAll($expected)
                )
            );

        $clinics = $data['clinics'];
        assert(is_array($clinics));

        assertDatabaseHas('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinics[0],
        ]);
    });

    it(description: 'replaces the previously assigned clinics', closure: function (): void {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new())
            ->createOne();

        $previousClinicId = $doctor->clinics()->firstOrFail()->id;

        $data = AssignClinicsToDoctorRequestFactory::new()->create();

        $response = postJson(url("/api/doctors/$doctor->id/clinics"), $data);

        $response->assertCreated();

        $clinics = $data['clinics'];
        assert(is_array($clinics));

        assertDatabaseHas('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinics[0],
        ]);

        assertDatabaseMissing('clinic_doctor', [
            'doctor_id' => $doctor->id,
            'clinic_id' => $previousClinicId,
        ]);
    });

    it(description: 'detaches every clinic when none is provided', closure: function (): void {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new()->count(2))
            ->createOne();

        $response = postJson(url("/api/doctors/$doctor->id/clinics"), []);

        $response->assertCreated();

        expect($doctor->clinics()->count())->toBe(0);
    });

    it(
        'cannot assign clinics with invalid data',
        closure: function (string $field, string|array $value, string $errorField): void {
            $doctor = DoctorFactory::new()->createOne();

            $response = postJson(url("/api/doctors/$doctor->id/clinics"), [$field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('assign-clinics-validation-rules');
});
