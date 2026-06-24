<?php

declare(strict_types=1);

namespace Lightit\Doctors\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Doctors\App\Requests\AssignClinicsToDoctorRequest;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Doctors\Domain\Actions\AssignClinicsToDoctorAction;
use Lightit\Doctors\Domain\Models\Doctor;

#[Group('Doctors')]
final readonly class AssignClinicsToDoctorController
{
    #[Endpoint(
        operationId: 'assignClinicsToDoctor',
        title: 'Assign clinics to a doctor',
        description: 'Assign zero, one or more clinics to a doctor',
    )]
    public function __invoke(
        AssignClinicsToDoctorRequest $request,
        Doctor $doctor,
        AssignClinicsToDoctorAction $action,
    ): JsonResponse {
        $doctor = $action->execute($doctor, $request->toDto());

        return DoctorResource::make($doctor)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
