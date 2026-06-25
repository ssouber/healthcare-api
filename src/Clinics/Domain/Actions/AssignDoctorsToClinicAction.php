<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Lightit\Clinics\Domain\Models\Clinic;

class AssignDoctorsToClinicAction
{
    public function execute(Clinic $clinic, array $doctors): Clinic
    {
        $clinic->doctors()->syncOrFail($doctors);

        return $clinic->loadCount('doctors');
    }
}
