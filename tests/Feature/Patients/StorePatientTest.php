<?php

declare(strict_types=1);

use Database\Factories\PatientFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Lightit\Patients\App\Controllers\StorePatientController;
use Lightit\Patients\App\Resources\PatientResource;
use Lightit\Patients\Domain\Models\Patient;
use Tests\RequestFactories\StorePatientRequestFactory;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

dataset('store-patient-validation-rules', [
    'name is required' => ['name', '', 'name'],
    'name must be a string' => ['name', ['array'], 'name'],
    'name not too short' => ['name', 'ams', 'name'],
    'name not too long' => ['name', Str::repeat('longg', 60), 'name'],

    'email is required' => ['email', '', 'email'],
    'email must be valid' => ['email', 'not-an-email', 'email'],

    'password is required' => ['password', '', 'password'],
    'password not too short' => ['password', 'Aa1!aa', 'password'],
    'password must have mixed case' => ['password', 'healthcare2027!', 'password'],
    'password must have numbers' => ['password', 'Healthcare!', 'password'],
    'password must have symbols' => ['password', 'Healthcare2027', 'password'],
]);

beforeEach(function (): void {
    Http::fake([
        'api.pwnedpasswords.com/*' => Http::response(''),
    ]);
});

describe('patients', function (): void {
    /** @see StorePatientController */
    it('can create a patient successfully', function (): void {
        $data = StorePatientRequestFactory::new()->create();

        $response = postJson(url('/api/patients'), $data);

        $patient = Patient::query()
            ->where('email', $data['email'])
            ->firstOrFail();

        /** @var array{data: array<string, mixed>} $resourceData */
        $resourceData = PatientResource::make($patient)->response()->getData(true);
        $expected = $resourceData['data'];

        $response
            ->assertCreated()
            ->assertJsonPath('data', $expected);

        assertDatabaseHas(Patient::class, [
            'id' => $patient->id,
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        /** @var string $password */
        $password = $data['password'];

        expect(Hash::check($password, $patient->password))->toBeTrue();
    });

    it('cannot create a patient with an already registered email', function (): void {
        $existingPatient = PatientFactory::new()->createOne();

        $data = StorePatientRequestFactory::new()->create([
            'email' => $existingPatient->email,
        ]);

        $response = postJson(url('/api/patients'), $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email'], 'error.fields');

        assertDatabaseMissing(Patient::class, [
            'name' => $data['name'],
        ]);
    });

    it(
        'cannot create a patient with invalid data',
        function (string $field, string|array $value, string $errorField): void {
            $data = StorePatientRequestFactory::new()->create();

            $response = postJson(url('/api/patients'), [...$data, $field => $value]);

            $response->assertUnprocessable()
                ->assertJsonValidationErrors([$errorField], 'error.fields');
        }
    )->with('store-patient-validation-rules');
});
