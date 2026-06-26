<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DoctorNotAvailableException extends Exception
{
    public function render(Request $request): Response
    {
        return response(['message' => 'Doctor not available.'], 409);
    }
}
