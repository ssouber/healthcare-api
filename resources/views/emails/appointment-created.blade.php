<x-mail::message>

# Your appointment is confirmed

Hello **{{ $patient->name }}**, your appointment has been successfully scheduled.

<x-mail::panel>
| | |
|---|---|
| **Doctor** | {{ $appointment->doctor->name }} |
| **Date** | {{ $appointment->starts_at->format('F j, Y') }} |
| **Time** | {{ $appointment->starts_at->format('g:i A') }} |
| **Clinic** | {{ $appointment->clinic->name }} |
</x-mail::panel>

If you need to reschedule or cancel, please contact us as soon as possible.

Thanks,<br>
{{ Config::string('app.name') }}

</x-mail::message>
