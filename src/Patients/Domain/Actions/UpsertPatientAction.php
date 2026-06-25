<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Actions;

use Lightit\Patients\Domain\DataTransferObjects\PatientDto;
use Lightit\Patients\Domain\Models\Patient;

class UpsertPatientAction
{
    public function execute(PatientDto $patientDto, Patient|null $patient = null): Patient
    {
        $patient ??= new Patient();
        $patient->name = $patientDto->name;
        $patient->email = $patientDto->email;

        if (! $patient->exists) {
            /** @var string $password */
            $password = $patientDto->password;
            $patient->password = $password;
        }

        $patient->saveOrFail();

        return $patient;
    }
}
