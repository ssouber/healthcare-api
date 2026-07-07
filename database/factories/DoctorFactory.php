<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lightit\Doctors\Domain\Models\Doctor;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function hasClinics(ClinicFactory $factory): self
    {
        return $this->has($factory, 'clinics');
    }
}
