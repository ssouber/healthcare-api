<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\DataTransferObjects;

readonly class AppointmentDto
{
    public function __construct(
        public int $patientId,
        public int $doctorId,
        public int $clinicId,
        public string $startsAt,
    ) {
    }
}
