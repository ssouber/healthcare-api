<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\App\Resources\ClinicResource;
use Lightit\Doctors\App\Resources\DoctorResource;
use Lightit\Patients\App\Resources\PatientResource;

/**
 * @mixin Appointment
 */
#[SchemaName('Appointment')]
class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor' => DoctorResource::make()->whenLoaded('doctor'),
            'patient' => PatientResource::make()->whenLoaded('patient'),
            'clinic' => ClinicResource::make()->whenLoaded('clinic'),
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ];
    }
}
