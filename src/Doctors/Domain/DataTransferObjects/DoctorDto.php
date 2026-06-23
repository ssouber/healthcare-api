<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\DataTransferObjects;

readonly class DoctorDto
{
    public function __construct(
        public string $name,
        public array|null $clinics,
    ) {
    }
}
