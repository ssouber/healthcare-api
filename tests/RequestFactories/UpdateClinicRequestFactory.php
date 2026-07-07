<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class UpdateClinicRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'address' => fake()->address(),
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }
}
