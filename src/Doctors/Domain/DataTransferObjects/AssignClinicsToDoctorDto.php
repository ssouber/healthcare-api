<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\DataTransferObjects;

readonly class AssignClinicsToDoctorDto
{
    public function __construct(
        public array $clinics,
    ) {
    }
}
