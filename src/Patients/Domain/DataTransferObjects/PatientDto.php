<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\DataTransferObjects;

readonly class PatientDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string|null $password = null,
    ) {
    }
}
