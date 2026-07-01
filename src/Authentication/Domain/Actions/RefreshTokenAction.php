<?php

declare(strict_types=1);

namespace Lightit\Authentication\Domain\Actions;

use Lightit\Authentication\Domain\DataTransferObjects\LoginResponseDto;
use PHPOpenSourceSaver\JWTAuth\Factory as JWTAuth;
use PHPOpenSourceSaver\JWTAuth\JWT;

final readonly class RefreshTokenAction
{
    public function __construct(
        private JWTAuth $jwtAuth,
        private JWT $jwt,
    ) {
    }

    public function execute(): LoginResponseDto
    {
        return new LoginResponseDto(
            accessToken: $this->jwt->refresh(),
            tokenType: 'Bearer',
            expiresIn: $this->jwtAuth->getTTL() * 60,
        );
    }
}
