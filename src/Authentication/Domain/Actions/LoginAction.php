<?php

declare(strict_types=1);

namespace Lightit\Authentication\Domain\Actions;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Lightit\Authentication\Domain\DataTransferObjects\LoginRequestDto;
use Lightit\Authentication\Domain\DataTransferObjects\LoginResponseDto;
use Lightit\Authentication\Domain\Exceptions\UnauthorizedException;
use PHPOpenSourceSaver\JWTAuth\Factory as JWTAuth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class LoginAction
{
    public function __construct(
        private readonly AuthFactory $factory,
        private readonly JWTAuth $jwtAuth,
    ) {
    }

    public function execute(LoginRequestDto $dto): LoginResponseDto
    {
        /** @var JWTGuard $guard */
        $guard = $this->factory->guard('api');

        $token = $guard->attempt(['email' => $dto->email, 'password' => $dto->password]);

        if (! $token) {
            throw new UnauthorizedException();
        }

        /** @var string $token */
        return new LoginResponseDto(
            accessToken: $token,
            tokenType: 'Bearer',
            expiresIn: $this->jwtAuth->getTTL() * 60,
        );
    }
}
