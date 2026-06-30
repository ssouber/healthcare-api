<?php

namespace Lightit\Authentication\Domain\Actions;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

final class LogoutAction
{
    public function __construct(
        private AuthFactory $factory,
    ) {
    }

    public function execute()
    {
        $guard = $this->factory->guard('api');
        $guard->logout();
    }
}
