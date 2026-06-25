<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Patients\Domain\DataTransferObjects\PatientDto;
use Lightit\Patients\Domain\Models\Patient;

class UpsertPatientAction
{
    public function execute(PatientDto $patientDto, ?Patient $patient = null): Patient
    {
        $patient ??= new Patient();
        $patient->name = $patientDto->name;
        $patient->email = $patientDto->email;

        if (!$patient->exists && $patientDto->password !== null)
        {
            $patient->password = $patientDto->password;
        }

        $patient->saveOrFail();

        return $patient;
    }
}
