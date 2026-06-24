<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Actions;

use Lightit\Doctors\Domain\DataTransferObjects\UpdateDoctorDto;
use Lightit\Doctors\Domain\Models\Doctor;

class UpdateDoctorAction
{
    public function execute(Doctor $doctor, string $name): Doctor
    {
        $doctor->name = $name;

        $doctor->saveOrFail();

        return $doctor;
    }
}
