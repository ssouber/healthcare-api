<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Carbon\CarbonImmutable;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;

class DeleteAppointmentAction
{
    public function execute(Appointment $appointment): void
    {
        if ($appointment->starts_at->isAfter(CarbonImmutable::now()->addHours(48))) {
            $appointment->deleteOrFail();
        } else {
            $appointment->status = AppointmentStatus::CANCELLED;
            $appointment->saveOrFail();
        }
    }
}
