<?php

declare(strict_types=1);

use Database\Factories\PatientFactory;
use Illuminate\Support\Str;
use Lightit\Patients\App\Controllers\UpdatePatientController;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Patients\Domain\Models\Patient;
use Tests\RequestFactories\UpdatePatientRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\putJson;

dataset(name: 'update-patient-validation-rules', dataset: [
    'name is required' => ['name', '', 'name'],
    'name must be a string' => ['name', ['array'], 'name'],
    'name not too short' => ['name', 'ams', 'name'],
    'name not too long' => ['name', Str::repeat(string: 'longg', times: 60), 'name'],

    'email is required' => ['email', '', 'email'],
    'email must be valid' => ['email', 'not-an-email', 'email'],
]);

describe('patients', function (): void {
    /** @see UpdatePatientController */
    it(description: 'can update a patient successfully', closure: function (): void {
        $existingPatient = PatientFactory::new()->name('old name')->createOne();

        $data = UpdatePatientRequestFactory::new()->name('new name')->create();

        $response = putJson(url("/api/patients/$existingPatient->id"), $data);

        $updatedPatient = Patient::query()
            ->where('email', $data['email'])
            ->firstOrFail();

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = PatientResource::make($updatedPatient)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertOk()
            ->assertJsonPath('data', $expected);

        assertDatabaseHas(Patient::class, [
            'id' => $updatedPatient->id,
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    });

    it(description: 'can update a patient keeping its own email', closure: function (): void {
        $existingPatient = PatientFactory::new()->createOne();

        $data = UpdatePatientRequestFactory::new()->email($existingPatient->email)->create();

        $response = putJson(url("/api/patients/$existingPatient->id"), $data);

        $response->assertOk();

        assertDatabaseHas(Patient::class, [
            'id' => $existingPatient->id,
            'name' => $data['name'],
            'email' => $existingPatient->email,
        ]);
    });

    it(description: 'does not change the password when updating a patient', closure: function (): void {
        $existingPatient = PatientFactory::new()->createOne();
        $originalPassword = $existingPatient->password;

        $data = UpdatePatientRequestFactory::new()->create();

        putJson(url("/api/patients/$existingPatient->id"), $data)
            ->assertOk();

        $existingPatient->refresh();

        expect($existingPatient->password)->toBe($originalPassword);
    });

    it(
        description: 'cannot update a patient with an email already used by another patient',
        closure: function (): void {
            $otherPatient = PatientFactory::new()->createOne();
            $existingPatient = PatientFactory::new()->createOne();
    
            $data = UpdatePatientRequestFactory::new()->email($otherPatient->email)->create();
    
            $response = putJson(url("/api/patients/$existingPatient->id"), $data);
    
            $response->assertUnprocessable()
                ->assertJsonValidationErrors(['email'], 'error.fields');
        }
    );

    it(description: 'returns not found when the patient does not exist', closure: function (): void {
        $data = UpdatePatientRequestFactory::new()->create();

        putJson(url('/api/patients/999999'), $data)
            ->assertNotFound();
    });

    it(
        'cannot update a patient with invalid data',
        closure: function (string $field, string|array $value, string $errorField): void {
            $existingPatient = PatientFactory::new()->createOne();

            $data = UpdatePatientRequestFactory::new()->create();

            $response = putJson(url("/api/patients/$existingPatient->id"), [...$data, $field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('update-patient-validation-rules');
});
