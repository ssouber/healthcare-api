<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Exceptions;

use Lightit\Shared\App\Exceptions\Http\HttpException;

class DoctorNotAvailableException extends HttpException
{
    #[\Override]
    protected int $status = 409;

    #[\Override]
    protected string $errorCode = 'doctor_not_available';

    #[\Override]
    protected $message = 'Doctor not available.';
}
