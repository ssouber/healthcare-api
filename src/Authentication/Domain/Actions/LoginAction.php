<?php

declare(strict_types=1);

namespace Lightit\Authentication\Domain\Actions;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
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

    /**
     * @param array{email: string, password: string} $credentials
     *
     * @throws UnauthorizedException
     */
    public function execute(array $credentials): LoginResponseDto
    {
        /** @var JWTGuard $guard */
        $guard = $this->factory->guard('api');

        $token = $guard->attempt($credentials);

        if (! $token) {
            throw new UnauthorizedException('Invalid credentials');
        }

        /** @var string $token */
        return new LoginResponseDto(
            accessToken: $token,
            tokenType: 'Bearer',
            expiresIn: $this->jwtAuth->getTTL() * 60,
        );
    }
}
