<?php

use App\Models\Schedule;
use App\Models\User;
use App\Services\ScheduleConflictDetector;

it('logs status changes', function () {
    $schedule = Schedule::factory()->create([
        'status' => Schedule::STATUS_DRAFT,
    ]);

    $schedule->status = Schedule::STATUS_COMPLETED;
    $schedule->save();

    $schedule->refresh();

    expect($schedule->auditLogs)->toHaveCount(1)
        ->and($schedule->auditLogs->first()->action)->toBe('status_changed')
        ->and($schedule->auditLogs->first()->metadata)->toMatchArray([
            'from' => Schedule::STATUS_DRAFT,
            'to' => Schedule::STATUS_COMPLETED,
        ]);
});

it('logs assignment add and removal', function () {
    $schedule = Schedule::factory()->create();
    $user = User::factory()->create();

    $schedule->logAssignmentAdded($user->id);
    $schedule->logAssignmentRemoved($user->id);

    expect($schedule->auditLogs)->toHaveCount(2)
        ->and($schedule->auditLogs->pluck('action')->all())->toBe([
            'assignment_added',
            'assignment_removed',
        ]);
});

it('detects location and personnel conflicts', function () {
    $detector = app(ScheduleConflictDetector::class);

    $scheduleA = Schedule::factory()->create([
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'status' => Schedule::STATUS_PUBLISHED,
    ]);

    $scheduleB = Schedule::factory()->create([
        'location_id' => $scheduleA->location_id,
        'scheduled_date' => $scheduleA->scheduled_date,
        'start_time' => '09:30:00',
        'end_time' => '11:00:00',
        'status' => Schedule::STATUS_OPEN,
    ]);

    $user = User::factory()->create();
    $scheduleA->users()->attach($user->id);
    $scheduleB->users()->attach($user->id);

    $summaryA = $detector->summary($scheduleA);

    expect($summaryA['location_count'])->toBeGreaterThan(0)
        ->and($summaryA['personnel_count'])->toBeGreaterThan(0)
        ->and($summaryA['has_conflicts'])->toBeTrue();
});
