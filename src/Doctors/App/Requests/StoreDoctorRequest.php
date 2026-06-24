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
            self::CLINICS => ['sometimes', 'array', Rule::in(Clinic::class)],
            self::CLINICS . '.*' => [Rule::numeric()->integer()],
        ];
    }

    public function toDto(): DoctorDto
    {
        return new DoctorDto(
            name: $this->string(self::NAME)->toString(),
            clinics: $this->array(self::CLINICS)
        );
    }
}
