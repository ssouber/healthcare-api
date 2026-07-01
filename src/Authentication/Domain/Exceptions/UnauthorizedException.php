<?php

declare(strict_types=1);

namespace Lightit\Authentication\Domain\Exceptions;

use Illuminate\Http\JsonResponse;
use Lightit\Shared\App\Exceptions\Http\HttpException;

class UnauthorizedException extends HttpException
{
    #[\Override]
    protected int $status = JsonResponse::HTTP_UNAUTHORIZED;

    #[\Override]
    protected string $errorCode = 'invalid_credentials';

    #[\Override]
    protected $message = 'Invalid credentials.';
}
