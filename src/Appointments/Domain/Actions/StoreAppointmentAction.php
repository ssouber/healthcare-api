<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Carbon\CarbonImmutable;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Exceptions\DoctorNotAvailableException;
use Lightit\Appointments\Domain\Exceptions\PatientNotAvailableException;
use Lightit\Appointments\Domain\Models\Appointment;
use Spatie\QueryBuilder\QueryBuilder;

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
        $appointment->starts_at = $dto->startsAt;
        $appointment->ends_at = $appointment->starts_at->addHour();

        $appointment->saveOrFail();
    }

    private function hasOverlap(string $column, int $id, CarbonImmutable $startsAt): bool
    {
        return QueryBuilder::for(Appointment::class)
            ->where($column, $id)
            ->where('starts_at', '<', $startsAt->addHour())
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }
}
