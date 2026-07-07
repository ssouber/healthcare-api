<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lightit\Appointments\Domain\Enums\AppointmentStatus;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = CarbonImmutable::parse(
            fake()->dateTimeBetween('+1 day', '+1 month')
        );

        return [
            'clinic_id' => ClinicFactory::new(),
            'doctor_id' => DoctorFactory::new(),
            'patient_id' => PatientFactory::new(),
            'status' => AppointmentStatus::SCHEDULED,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHour(),
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(
            function (Appointment $appointment): void {
                $appointment->doctor
                    ->clinics()
                    ->syncWithoutDetaching($appointment->clinic_id);
            }
        );
    }

    public function forClinic(Clinic|ClinicFactory $clinic): self
    {
        return $this->for($clinic, 'clinic');
    }

    public function forDoctor(Doctor|DoctorFactory $doctor): self
    {
        return $this->for($doctor, 'doctor');
    }

    public function forPatient(Patient|PatientFactory $patient): self
    {
        return $this->for($patient, 'patient');
    }

    public function startsAt(CarbonImmutable $startsAt): self
    {
        return $this->state([
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHour(),
        ]);
    }

    public function status(AppointmentStatus $status): self
    {
        return $this->set('status', $status);
    }
}
