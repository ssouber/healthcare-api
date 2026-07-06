<?php

declare(strict_types=1);

namespace Tests\RequestFactories;

use Carbon\CarbonImmutable;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Shared\Domain\Enums\DateFormat;
use Worksome\RequestFactories\RequestFactory;

class UpsertAppointmentRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $doctor = DoctorFactory::new()
            ->hasClinics(ClinicFactory::new())
            ->createOne();

        $startsAt = CarbonImmutable::parse(
            fake()->dateTimeBetween('+1 day', '+1 month')
        );

        return [
            'doctor' => $doctor->id,
            'clinic' => $doctor->clinics()->value('id'),
            'starts_at' => $startsAt->format(DateFormat::DATETIME->value),
        ];
    }

    public function startsAt(CarbonImmutable $startsAt): self
    {
        return $this->state(['start_at' => $startsAt->format(DateFormat::DATETIME->value)]);
    }
}
