<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $startsAt = CarbonImmutable::instance($this->faker->dateTimeBetween('now', '+1 month'));

        /** @var Clinic $clinic */
        $clinic = ClinicFactory::new()->create();

        /** @var Doctor $doctor */
        $doctor = DoctorFactory::new()->create();
        $doctor->clinics()->attachOrFail($clinic);

        return [
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHour(),
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctor->id,
            'patient_id' => PatientFactory::new(),
            'status' => AppointmentStatus::SCHEDULED,
        ];
    }
}
