<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Database\Factories\ClinicFactory;
use Worksome\RequestFactories\RequestFactory;

class StoreDoctorRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'clinics' => [ClinicFactory::new()],
        ];
    }
}
