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
            self::CLINICS => ['sometimes', 'array'],
            self::CLINICS . '.*' => [Rule::numeric()->integer(), Rule::exists(Clinic::class, 'id')],
        ];
    }

    public function getClinics(): array
    {
        return $this->array(self::CLINICS);
    }
}
