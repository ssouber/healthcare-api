<?php

declare(strict_types=1);

namespace Lightit\Authentication\Domain\DataTransferObjects;

readonly class LoginResponseDto
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
    ) {
    }
}
