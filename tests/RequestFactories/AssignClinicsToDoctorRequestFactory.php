<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Database\Factories\ClinicFactory;
use Worksome\RequestFactories\RequestFactory;

class AssignClinicsToDoctorRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'clinics' => [ClinicFactory::new()],
        ];
    }
}
