<?php

declare(strict_types=1);

namespace Lightit\Appointments\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;
use Lightit\Shared\Domain\Enums\DateFormat;

class AppointmentCreatedNotification extends Notification implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public function __construct(protected Appointment $appointment)
    {
    }

    public function via(Patient $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Patient $notifiable): MailMessage
    {
        $appointment = $this->appointment;

        return new MailMessage()
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("Your appointment has been confirmed with the following details: \n")
                    ->line("Doctor: {$appointment->doctor->name}")
                    ->line("Patient: {$appointment->patient->name}")
                    ->line("Date: {$appointment->starts_at->format(DateFormat::DATETIME->value)}")
                    ->line("Clinic: {$appointment->clinic->name}\n")
                    ->salutation('Have a nice day!');
    }
}
