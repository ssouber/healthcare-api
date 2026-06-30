<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\ListAppointmentAction;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Appointments')]
final readonly class ListAppointmentController
{
    #[Endpoint(
        operationId: 'listAppointments',
        title: 'List appointments',
        description: 'Retrieves a list of appointments .'
    )]
    public function __invoke(
        ListAppointmentAction $action,
        #[CurrentUser] Patient $patient,
    ): JsonResponse {
        $appointments = $action->execute($patient);

        return AppointmentResource::collection($appointments)
            ->response();
    }
}
