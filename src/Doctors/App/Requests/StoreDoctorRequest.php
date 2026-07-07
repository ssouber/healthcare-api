<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\DataTransferObjects\DoctorDto;

class StoreDoctorRequest extends FormRequest
{
    public const string NAME = 'name';

    public const string CLINICS = 'clinics';

    public function rules(): array
    {
        return [
            self::NAME => ['required', 'string', 'min:4', 'max:255'],
            self::CLINICS => ['sometimes', 'bail', 'array', Rule::exists(Clinic::class, 'id')],
            self::CLINICS . '.*' => [Rule::numeric()->integer()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $clinics = $this->input(self::CLINICS);

        if (! is_array($clinics)) {
            return;
        }

        $this->merge([
            self::CLINICS => array_map(
                static fn (mixed $clinic): int|null => is_int($clinic) ? $clinic : null,
                $clinics
            ),
        ]);
    }

    public function toDto(): DoctorDto
    {
        return new DoctorDto(
            name: $this->string(self::NAME)->toString(),
            clinics: $this->array(self::CLINICS)
        );
    }
}
