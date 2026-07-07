<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Database\Factories\DoctorFactory;
use Worksome\RequestFactories\RequestFactory;

class StoreClinicRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'address' => fake()->address(),
            'doctors' => [DoctorFactory::new()],
        ];
    }

    public function doctors(array $doctors): self
    {
        return $this->state(['doctors' => $doctors]);
    }
}
