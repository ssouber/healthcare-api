<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Clinics\Domain\Models\Clinic;

class AssignClinicsToDoctorRequest extends FormRequest
{
    public const string CLINICS = 'clinics';

    public function rules(): array
    {
        return [
            self::CLINICS => ['required', 'array', Rule::exists(Clinic::class)],
            self::CLINICS . '.*' => [Rule::numeric()],
        ];
    }

    public function getClinics(): array
    {
        return $this->array(self::CLINICS);
    }
}
