<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\Models\Doctor;

class AssignClinicsToDoctorAction
{
    public function execute(Doctor $doctor, array $clinics): Doctor
    {
        $doctor->clinics()->syncOrFail($clinics);

        return $doctor->load('clinics');
    }
}
