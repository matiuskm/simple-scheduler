<?php

use App\Models\Schedule;
use App\Models\User;
use App\Notifications\ScheduleAssignmentConfirmed;
use App\Notifications\ScheduleCancelled;
use App\Notifications\ScheduleForcedRelease;
use App\Services\ScheduleAssignmentService;
use Illuminate\Support\Facades\Notification;

it('sends an assignment confirmation email when a user is assigned', function () {
    Notification::fake();
    config()->set('scheduler.email_notifications', true);

    $schedule = Schedule::factory()->create();
    $user = User::factory()->create();

    app(ScheduleAssignmentService::class)->assign($schedule, $user, $user);

    Notification::assertSentTo($user, ScheduleAssignmentConfirmed::class);
});

it('sends a forced release email when an admin removes a user', function () {
    Notification::fake();
    config()->set('scheduler.email_notifications', true);

    $schedule = Schedule::factory()->create();
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);

    $schedule->users()->attach($user->id, ['assigned_by' => $admin->id]);

    app(ScheduleAssignmentService::class)->release($schedule, $user, $admin, true);

    Notification::assertSentTo($user, ScheduleForcedRelease::class);
});

it('sends a cancellation email to assigned users when a schedule is cancelled', function () {
    Notification::fake();
    config()->set('scheduler.email_notifications', true);

    $schedule = Schedule::factory()->create(['status' => Schedule::STATUS_PUBLISHED]);
    $user = User::factory()->create();
    $schedule->users()->attach($user->id, ['assigned_by' => $user->id]);

    $schedule->status = Schedule::STATUS_CANCELLED;
    $schedule->save();

    Notification::assertSentTo($user, ScheduleCancelled::class);
});

