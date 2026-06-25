<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Patients\Domain\DataTransferObjects\StorePatientDto;
use Lightit\Patients\Domain\Models\Patient;

class StorePatientAction
{
    public function execute(StorePatientDto $patientDto): Patient
    {
        $patient = new Patient();
        $patient->name = $patientDto->name;
        $patient->email = $patientDto->email;
        $patient->password = $patientDto->password;

        $patient->saveOrFail();

        return $patient;
    }
}
