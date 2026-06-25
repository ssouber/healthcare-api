<?php

declare(strict_types=1);

namespace Lightit\Clinics\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Lightit\Clinics\App\Requests\AssignDoctorsToClinicRequest;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Clinics\Domain\Actions\AssignDoctorsToClinicAction;
use Lightit\Clinics\Domain\Models\Clinic;

#[Group('Clinics')]
final readonly class AssignDoctorsToClinicController
{
    #[Endpoint(
        operationId: 'assignDoctorsToClinic',
        title: 'Assign doctors to a clinic',
        description: 'Assign zero, one or more doctors to a clinic.'
    )]
    public function __invoke(
        AssignDoctorsToClinicRequest $request,
        Clinic $clinic,
        AssignDoctorsToClinicAction $action,
    ): JsonResponse {
        $clinic = $action->execute($clinic, $request->getDoctors());

        return ClinicResource::make($clinic)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
