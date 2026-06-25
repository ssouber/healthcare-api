<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Doctors\Domain\Models\Doctor;

class AssignDoctorsToClinicRequest extends FormRequest
{
    public const string DOCTORS = 'doctors';

    public function rules(): array
    {
        return [
            self::DOCTORS => ['sometimes', 'array', Rule::exists(Doctor::class, 'id')],
            self::DOCTORS . '.*' => [Rule::numeric()->integer()],
        ];
    }

    public function getDoctors(): array
    {
        return $this->array(self::DOCTORS);
    }
}
