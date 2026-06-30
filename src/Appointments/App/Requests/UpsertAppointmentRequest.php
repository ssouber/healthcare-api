<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Shared\Domain\Enums\DateFormat;

class UpsertAppointmentRequest extends FormRequest
{
    public const string DOCTOR = 'doctor';

    public const string CLINIC = 'clinic';

    public const string STARTS_AT = 'starts_at';

    public function rules(): array
    {
        return [
            self::DOCTOR   => ['required', Rule::numeric()->integer(), Rule::exists(Doctor::class, 'id')],
            self::CLINIC   => [
                'required',
                Rule::numeric()->integer(),
                Rule::exists(Clinic::class, 'id'),
                Rule::exists('clinic_doctor', 'clinic_id')
                    ->where('doctor_id', $this->integer(self::DOCTOR)),
            ],
            self::STARTS_AT => ['required', Rule::date()->format(DateFormat::DATETIME->value)->after('now')],
        ];
    }

    public function toDto(): AppointmentDto
    {
        return new AppointmentDto(
            doctorId: $this->integer(self::DOCTOR),
            clinicId: $this->integer(self::CLINIC),
            startsAt: CarbonImmutable::parse($this->string(self::STARTS_AT)->toString()),
        );
    }
}
