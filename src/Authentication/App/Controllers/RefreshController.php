<?php

declare(strict_types=1);

namespace Lightit\Authentication\App\Controllers;

use Illuminate\Http\JsonResponse;
use Lightit\Authentication\App\Resources\AuthTokenResource;
use Lightit\Authentication\Domain\Actions\RefreshTokenAction;

final class RefreshController
{
    public function __invoke(RefreshTokenAction $action): JsonResponse
    {
        $tokenData = $action->execute();
        return AuthTokenResource::make($tokenData)->response();
    }
}
