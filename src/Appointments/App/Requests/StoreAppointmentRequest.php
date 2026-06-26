<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;

class StoreAppointmentRequest extends FormRequest
{
    public const DOCTOR = 'doctor';

    public const string CLINIC = 'clinic';

    public const string PATIENT = 'patient';

    public const string STARTS_AT = 'starts_at';

    public function rules(): array
    {
        return [
            self::DOCTOR => ['required', 'integer', Rule::exists(Doctor::class, 'id')],
            self::CLINIC => [
                'required',
                'integer',
                Rule::exists(Clinic::class, 'id'),
                Rule::exists('clinic_doctor', 'clinic_id')
                    ->where('doctor_id', $this->integer(self::DOCTOR))],
            self::PATIENT => ['required', Rule::numeric(), Rule::exists(Patient::class, 'id')],
            self::STARTS_AT => ['required', Rule::date()->format('Y-m-d H:i')->after('now')],
        ];
    }

    public function toDto(): AppointmentDto
    {
        return new AppointmentDto(
            patientId: $this->integer(self::PATIENT),
            doctorId: $this->integer(self::DOCTOR),
            clinicId: $this->integer(self::CLINIC),
            startsAt: $this->string(self::STARTS_AT)->toString(),
        );
    }
}
