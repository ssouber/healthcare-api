<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Carbon\CarbonImmutable;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Exceptions\DoctorNotAvailableException;
use Lightit\Appointments\Domain\Exceptions\PatientNotAvailableException;
use Lightit\Appointments\Domain\Models\Appointment;

class StoreAppointmentAction
{
    /**
     * @throws DoctorNotAvailableException
     * @throws PatientNotAvailableException
     */
    public function execute(AppointmentDto $dto): Appointment
    {
        if ($this->hasOverlap('doctor_id', $dto->doctorId, $dto->startsAt)) {
            throw new DoctorNotAvailableException();
        }

        if ($this->hasOverlap('patient_id', $dto->patientId, $dto->startsAt)) {
            throw new PatientNotAvailableException();
        }

        $appointment = new Appointment();
        $appointment->doctor_id = $dto->doctorId;
        $appointment->patient_id = $dto->patientId;
        $appointment->clinic_id = $dto->clinicId;
        $appointment->starts_at = $dto->startsAt;
        $appointment->ends_at = $dto->startsAt->addHour();
        $appointment->status = AppointmentStatus::SCHEDULED;

        $appointment->saveOrFail();

        return $appointment->load('doctor', 'patient', 'clinic');
    }

    private function hasOverlap(string $column, int $id, CarbonImmutable $startsAt): bool
    {
        return Appointment::query()
            ->where($column, $id)
            ->where('starts_at', '<', $startsAt->addHour())
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }
}
