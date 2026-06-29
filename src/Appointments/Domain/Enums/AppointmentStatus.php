<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Enums;

enum AppointmentStatus: string
{
    case SCHEDULED = 'scheduled';
    case CANCELLED = 'cancelled';
}
