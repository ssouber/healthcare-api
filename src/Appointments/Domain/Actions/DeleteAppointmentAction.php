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
        if ($this->canBeDeleted($appointment->starts_at)) {
            $appointment->deleteOrFail();
        } else {
            $appointment->status = AppointmentStatus::CANCELLED;
            $appointment->saveOrFail();
        }
    }

    private function canBeDeleted(CarbonImmutable $startsAt): bool
    {
        return $startsAt->isAfter(CarbonImmutable::now()->addHours(48));
    }
}
