<?php

declare(strict_types=1);

namespace Lightit\Patients\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Patients\Domain\DataTransferObjects\UpdatePatientDto;
use Lightit\Patients\Domain\Models\Patient;

class UpdatePatientRequest extends FormRequest
{
    public const string NAME = 'name';

    public const string EMAIL = 'email';

    public function rules(): array
    {
        return [
            self::NAME  => ['required', 'string', 'min:4', 'max:255'],
            self::EMAIL => ['required', 'email', Rule::unique(Patient::class, 'email')->ignore($this->patient)],
        ];
    }

    public function toDto(): UpdatePatientDto
    {
        return new UpdatePatientDto(
            name: $this->string(self::NAME)->toString(),
            email: $this->string(self::EMAIL)->toString(),
        );
    }
}
