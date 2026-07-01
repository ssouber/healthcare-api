<?php

declare(strict_types=1);

namespace Lightit\Authentication\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Illuminate\Http\JsonResponse;
use Lightit\Authentication\App\Requests\LoginRequest;
use Lightit\Authentication\App\Resources\AuthTokenResource;
use Lightit\Authentication\Domain\Actions\LoginAction;

final class LoginController
{
    #[Endpoint(
        operationId: 'loginPatient',
        title: 'Login Patient',
        description: 'Logs in a patient.'
    )]
    public function __invoke(LoginRequest $request, LoginAction $loginAction): JsonResponse
    {
        $tokenData = $loginAction->execute($request->toDto());

        return AuthTokenResource::make($tokenData)->response();
    }
}
