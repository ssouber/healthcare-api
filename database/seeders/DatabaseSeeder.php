<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserFactory::new()->createMany(35);
        DoctorFactory::new()->createMany(35);
        ClinicFactory::new()->createMany(10);
        PatientFactory::new()->createMany(15);
    }
}
