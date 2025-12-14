<?php

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;

it('derives open and full lifecycle from assignments and required personnel', function () {
    config()->set('scheduler.lock_window_minutes', 30);

    $schedule = Schedule::factory()->create([
        'required_personnel' => 2,
        'status' => Schedule::STATUS_PUBLISHED,
    ]);

    expect($schedule->lifecycle_status)->toBe(Schedule::STATUS_OPEN);

    $schedule->users()->attach(User::factory()->create());
    $schedule->refresh();

    expect($schedule->lifecycle_status)->toBe(Schedule::STATUS_OPEN);

    $schedule->users()->attach(User::factory()->create());
    $schedule->refresh();

    expect($schedule->lifecycle_status)->toBe(Schedule::STATUS_FULL)
        ->and($schedule->isFull)->toBeTrue()
        ->and($schedule->canAssign())->toBeFalse();
});

it('locks schedules inside the lock window for non-admins', function () {
    Carbon::setTestNow(now());
    config()->set('scheduler.lock_window_minutes', 60);

    $schedule = Schedule::factory()->create([
        'scheduled_date' => now()->toDateString(),
        'start_time' => now()->copy()->addMinutes(30)->format('H:i:s'),
        'required_personnel' => 1,
        'status' => Schedule::STATUS_PUBLISHED,
    ]);

    expect($schedule->is_locked)->toBeTrue()
        ->and($schedule->lifecycle_status)->toBe(Schedule::STATUS_LOCKED)
        ->and($schedule->canAssign())->toBeFalse()
        ->and($schedule->canAssign(true))->toBeTrue();

    expect(fn () => $schedule->assertCanAssign())->toThrow(DomainException::class);
    expect(fn () => $schedule->assertCanAssign(true))->not->toThrow(DomainException::class);
});

it('blocks assignments when completed or cancelled', function () {
    $cancelled = Schedule::factory()->create([
        'status' => Schedule::STATUS_CANCELLED,
    ]);

    $completed = Schedule::factory()->create([
        'status' => Schedule::STATUS_COMPLETED,
    ]);

    expect($cancelled->canAssign())->toBeFalse()
        ->and($completed->canAssign())->toBeFalse()
        ->and($cancelled->lifecycle_status)->toBe(Schedule::STATUS_CANCELLED)
        ->and($completed->lifecycle_status)->toBe(Schedule::STATUS_COMPLETED);
});
