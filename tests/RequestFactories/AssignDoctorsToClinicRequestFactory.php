<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Database\Factories\DoctorFactory;
use Worksome\RequestFactories\RequestFactory;

class AssignDoctorsToClinicRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'doctors' => [DoctorFactory::new()],
        ];
    }
}
