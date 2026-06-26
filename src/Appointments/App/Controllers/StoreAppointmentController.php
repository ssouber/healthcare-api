<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Appointments\App\Requests\StoreAppointmentRequest;
use Lightit\Appointments\App\Resources\AppointmentResource;
use Lightit\Appointments\Domain\Actions\StoreAppointmentAction;

#[Group('Appointments')]
final readonly class StoreAppointmentController
{
    #[Endpoint(
        operationId: 'storeAppointment',
        title: 'Schedule an appointment',
        description: 'Create a new appointment',
    )]
    public function __invoke(
        StoreAppointmentRequest $request,
        StoreAppointmentAction $storeAppointmentAction,
    ): JsonResponse {
        $appointment = $storeAppointmentAction->execute($request->toDto());

        return AppointmentResource::make($appointment)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
