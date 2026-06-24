<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lightit\Doctors\Domain\DataTransferObjects\AssignClinicsToDoctorDto;

class AssignClinicsToDoctorRequest extends FormRequest
{
    public const string CLINICS = 'clinics';

    public function rules(): array
    {
        return [
            self::CLINICS => ['required', 'array'],
            self::CLINICS . '.*' => ['integer', 'exists:clinics,id'],
        ];
    }

    public function toDto(): AssignClinicsToDoctorDto
    {
        return new AssignClinicsToDoctorDto(
            clinics: $this->array(self::CLINICS)
        );
    }
}
