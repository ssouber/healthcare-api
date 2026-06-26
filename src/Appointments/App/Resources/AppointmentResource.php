<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Resources;

use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lightit\Appointments\Domain\Models\Appointment;

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
            'doctor_name' => $this->whenLoaded('doctor', fn () => $this->doctor->name),
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient->name),
            'clinic_name' => $this->whenLoaded('clinic', fn () => $this->clinic->name),
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ];
    }
}
