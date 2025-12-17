<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\User;
use App\Notifications\ScheduleAssignmentConfirmed;
use App\Notifications\ScheduleCancelled;
use App\Notifications\ScheduleForcedRelease;

class ScheduleAssignmentService
{
    public function assign(Schedule $schedule, User $user, ?User $actor = null): void
    {
        $asAdmin = (bool) ($actor?->isAdmin());

        $schedule->assertCanAssign($asAdmin);

        if ($schedule->users()->where('users.id', $user->id)->exists()) {
            return;
        }

        $schedule->users()->attach($user->id, [
            'assigned_by' => $actor?->id ?? $user->id,
        ]);

        $schedule->logAssignmentAdded($user->id);

        if ($this->notificationsEnabled()) {
            $user->notify(new ScheduleAssignmentConfirmed($schedule));
        }
    }

    public function release(Schedule $schedule, User $user, ?User $actor = null, bool $forced = false): void
    {
        $asAdmin = (bool) ($actor?->isAdmin());

        $schedule->assertCanRelease($asAdmin);

        $schedule->users()->detach($user->id);
        $schedule->logAssignmentRemoved($user->id);

        if ($forced && $this->notificationsEnabled()) {
            $user->notify(new ScheduleForcedRelease($schedule, $actor));
        }
    }

    public function notifyScheduleCancelled(Schedule $schedule, ?User $actor = null): void
    {
        if (! $this->notificationsEnabled()) {
            return;
        }

        $schedule->loadMissing('users', 'location');

        foreach ($schedule->users as $user) {
            $user->notify(new ScheduleCancelled($schedule, $actor));
        }
    }

    private function notificationsEnabled(): bool
    {
        return (bool) config('scheduler.email_notifications', true);
    }
}

