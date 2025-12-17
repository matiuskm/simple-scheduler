<?php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleAssignmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Schedule $schedule)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $schedule = $this->schedule->loadMissing('location');

        return (new MailMessage)
            ->subject('Schedule assignment confirmed')
            ->line("You have been assigned to: {$schedule->title}")
            ->line("When: {$schedule->scheduled_date} {$schedule->start_time}")
            ->line('Location: ' . ($schedule->location?->name ?? '—'))
            ->action('View Dashboard', url('/admin'));
    }
}

