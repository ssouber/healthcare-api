<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;
use Lightit\Appointments\Domain\Actions\DeleteAppointmentAction;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Appointments')]
final readonly class DeleteAppointmentController
{
    #[Endpoint(
        operationId: 'deleteAppointment',
        title: 'Delete an appointment',
        description: 'Deletes an appointment by its ID.'
    )]
    public function __invoke(
        Appointment $appointment,
        DeleteAppointmentAction $deleteAppointmentAction,
        #[CurrentUser]
        Patient $patient,
    ): Response {
        $deleteAppointmentAction->execute($appointment, $patient);

        return response()->noContent();
    }
}
