<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Lightit\Patients\Domain\DataTransferObjects\PatientDto;
use Lightit\Patients\Domain\Models\Patient;

class StorePatientRequest extends FormRequest
{
    public const string NAME = 'name';

    public const string EMAIL = 'email';

    public const string PASSWORD = 'password';

    public function rules(): array
    {
        return [
            self::NAME     => ['required', 'string', 'min:4', 'max:255'],
            self::EMAIL    => ['required', Rule::email(), Rule::unique(Patient::class, 'email')],
            self::PASSWORD => ['required', Password::default()],
        ];
    }

    public function toDto(): PatientDto
    {
        return new PatientDto(
            name: $this->string(self::NAME)->toString(),
            email: $this->string(self::EMAIL)->toString(),
            password: $this->string(self::PASSWORD)->toString(),
        );
    }
}
