<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Carbon\CarbonImmutable;
use Lightit\Appointments\App\Notifications\AppointmentCreatedNotification;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Exceptions\DoctorNotAvailableException;
use Lightit\Appointments\Domain\Exceptions\PatientNotAvailableException;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;

class UpsertAppointmentAction
{
    /**
     * @throws DoctorNotAvailableException
     * @throws PatientNotAvailableException
     */
    public function execute(
        AppointmentDto $dto,
        Patient $patient,
        Appointment|null $appointment = null,
    ): Appointment {
        $isNewAppointment = $appointment == null;
        $appointment ??= new Appointment();

        if ($this->hasOverlap('doctor_id', $dto->doctorId, $dto->startsAt, $appointment->id)) {
            throw new DoctorNotAvailableException();
        }

        if ($this->hasOverlap('patient_id', $patient->id, $dto->startsAt, $appointment->id)) {
            throw new PatientNotAvailableException();
        }

        $appointment->doctor_id = $dto->doctorId;
        $appointment->patient_id = $patient->id;
        $appointment->clinic_id = $dto->clinicId;
        $appointment->starts_at = $dto->startsAt;
        $appointment->ends_at = $dto->startsAt->addHour();
        $appointment->status = AppointmentStatus::SCHEDULED;

        $appointment->saveOrFail();

        if ($isNewAppointment) {
            $patient->notify(new AppointmentCreatedNotification($appointment));
        }

        return $appointment->load('doctor', 'patient', 'clinic');
    }

    private function hasOverlap(string $column, int $id, CarbonImmutable $startsAt, int|null $excludeId = null): bool
    {
        $query = Appointment::query()
            ->where($column, $id)
            ->where('starts_at', '<', $startsAt->addHour())
            ->where('ends_at', '>', $startsAt);

        if ($excludeId !== null) {
            $query->whereKeyNot($excludeId);
        }

        return $query->exists();
    }
}
