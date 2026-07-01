<?php

declare(strict_types=1);

namespace Lightit\Authentication\Domain\DataTransferObjects;

readonly class LoginRequestDto
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
