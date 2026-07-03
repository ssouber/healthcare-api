<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class UpdatePatientRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function email(string $email): self
    {
        return $this->state(['email' => $email]);
    }
}
