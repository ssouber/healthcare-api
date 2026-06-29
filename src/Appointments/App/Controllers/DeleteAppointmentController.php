<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Lightit\Appointments\Domain\Actions\DeleteAppointmentAction;
use Lightit\Appointments\Domain\Models\Appointment;

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
    ): Response {
        $deleteAppointmentAction->execute($appointment);

        return response()->noContent();
    }
}
