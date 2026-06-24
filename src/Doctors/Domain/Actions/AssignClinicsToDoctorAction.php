<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\DataTransferObjects\AssignClinicsToDoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class AssignClinicsToDoctorAction
{
    public function execute(Doctor $doctor, AssignClinicsToDoctorDto $assignClinicsToDoctorDto): Doctor
    {
        $doctor->clinics()->syncOrFail($assignClinicsToDoctorDto->clinics);

        return $doctor->load('clinics');
    }
}
