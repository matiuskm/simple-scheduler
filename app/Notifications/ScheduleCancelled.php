<?php

namespace App\Notifications;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Schedule $schedule,
        public readonly ?User $actor = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $schedule = $this->schedule->loadMissing('location');
        $actorName = $this->actor?->name ?? 'an admin';

        return (new MailMessage)
            ->subject('Schedule cancelled')
            ->line("A schedule you were assigned to was cancelled: {$schedule->title}")
            ->line("When: {$schedule->scheduled_date} {$schedule->start_time}")
            ->line('Location: ' . ($schedule->location?->name ?? '—'))
            ->line("Cancelled by: {$actorName}")
            ->action('View Dashboard', url('/admin'));
    }
}

