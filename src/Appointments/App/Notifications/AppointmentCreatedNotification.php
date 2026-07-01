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
        return new MailMessage()
            ->markdown('emails.appointment-created', [
                'appointment' => $this->appointment,
                'patient'     => $notifiable,
            ]);
    }
}
