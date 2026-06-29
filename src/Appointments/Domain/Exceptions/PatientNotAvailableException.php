<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Exceptions;

use Illuminate\Http\JsonResponse;
use Lightit\Shared\App\Exceptions\Http\HttpException;

class PatientNotAvailableException extends HttpException
{
    #[\Override]
    protected int $status = JsonResponse::HTTP_CONFLICT;

    #[\Override]
    protected string $errorCode = 'patient_not_available';

    #[\Override]
    protected $message = 'Patient not available.';
}
