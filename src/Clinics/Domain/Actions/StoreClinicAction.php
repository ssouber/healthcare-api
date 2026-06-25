<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Actions;

use Illuminate\Support\Facades\DB;
use Lightit\Clinics\Domain\DataTransferObjects\StoreClinicDto;
use Lightit\Clinics\Domain\Models\Clinic;

class StoreClinicAction
{
    public function execute(StoreClinicDto $clinicDto): Clinic
    {
        return DB::transaction(function () use ($clinicDto) {
            $clinic = new Clinic();
            $clinic->name = $clinicDto->name;
            $clinic->address = $clinicDto->address;

            $clinic->saveOrFail();

            if ($clinicDto->doctors !== null) {
                $clinic->doctors()->syncOrFail($clinicDto->doctors);
            }

            return $clinic->loadCount('doctors');
        });
    }
}
