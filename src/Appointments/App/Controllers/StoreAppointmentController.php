<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Requests\UpsertAppointmentRequest;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\UpsertAppointmentAction;
use Lightit\Patients\Domain\Models\Patient;

#[Group('Appointments')]
final readonly class StoreAppointmentController
{
    #[Endpoint(
        operationId: 'storeAppointment',
        title: 'Schedule an appointment',
        description: 'Create a new appointment',
    )]
    public function __invoke(
        UpsertAppointmentRequest $request,
        UpsertAppointmentAction $storeAppointmentAction,
        #[CurrentUser]
        Patient $patient,
    ): JsonResponse {
        $appointment = $storeAppointmentAction->execute($request->toDto($patient->id));

        return AppointmentResource::make($appointment)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
