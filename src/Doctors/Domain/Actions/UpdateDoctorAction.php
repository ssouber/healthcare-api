<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\DataTransferObjects\UpdateDoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class UpdateDoctorAction
{
    public function execute(Doctor $doctor, UpdateDoctorDto $updateDoctorDto): Doctor
    {
        $doctor->name = $updateDoctorDto->name;

        $doctor->saveOrFail();

        return $doctor;
    }
}
