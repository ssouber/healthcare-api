<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Carbon\CarbonImmutable;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;
use Lightit\Shared\App\Exceptions\Http\ForbiddenException;

class DeleteAppointmentAction
{
    public function execute(Appointment $appointment, Patient $patient): void
    {
        if ($appointment->patient_id !== $patient->id) {
            throw new ForbiddenException('You are not authorized to delete this appointment.');
        } elseif ($appointment->starts_at->isAfter(CarbonImmutable::now()->addHours(48))) {
            $appointment->deleteOrFail();
        } else {
            $appointment->status = AppointmentStatus::CANCELLED;
            $appointment->saveOrFail();
        }
    }
}
