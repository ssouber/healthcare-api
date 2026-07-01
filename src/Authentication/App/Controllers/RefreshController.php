<?php

declare(strict_types=1);

namespace Lightit\Authentication\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Illuminate\Http\JsonResponse;
use Lightit\Authentication\App\Resources\AuthTokenResource;
use Lightit\Authentication\Domain\Actions\RefreshTokenAction;

final class RefreshController
{
    #[Endpoint(
        operationId: 'refreshToken',
        title: 'Refresh Token',
        description: 'Refresh logged patient token.',
    )]
    public function __invoke(RefreshTokenAction $action): JsonResponse
    {
        $tokenData = $action->execute();

        return AuthTokenResource::make($tokenData)->response();
    }
}
