<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PatientNotAvailableException extends Exception
{
    public function render(Request $request): Response
    {
        return response(['message' => 'Patient not available.'], 409);
    }
}
