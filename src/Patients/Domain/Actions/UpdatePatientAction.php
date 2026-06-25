<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Patients\Domain\DataTransferObjects\UpdatePatientDto;
use Lightit\Patients\Domain\Models\Patient;

class UpdatePatientAction
{
    public function execute(Patient $patient, UpdatePatientDto $patientDto): Patient
    {
        $patient->name = $patientDto->name;
        $patient->email = $patientDto->email;

        $patient->saveOrFail();

        return $patient;
    }
}
