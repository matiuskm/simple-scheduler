<?php

use App\Models\AssignmentRequest;
use App\Models\Schedule;
use App\Models\User;

it('approves requests and assigns user with role', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $schedule = Schedule::factory()->create(['required_personnel' => 1]);
    $user = User::factory()->create();

    $request = AssignmentRequest::factory()->create([
        'schedule_id' => $schedule->id,
        'user_id' => $user->id,
    ]);

    auth()->login($admin);

    $request->approve($admin);

    $request->refresh();
    $schedule->refresh();

    expect($request->status)->toBe(AssignmentRequest::STATUS_APPROVED)
        ->and($request->decided_by)->toBe($admin->id)
        ->and($schedule->users()->where('user_id', $user->id)->exists())->toBeTrue()
        ->and($schedule->auditLogs()->where('action', 'assignment_request_approved')->exists())->toBeTrue();
});

it('rejects requests and records audit', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $schedule = Schedule::factory()->create();
    $user = User::factory()->create();

    $request = AssignmentRequest::factory()->create([
        'schedule_id' => $schedule->id,
        'user_id' => $user->id,
    ]);

    auth()->login($admin);
    $request->reject($admin, 'Not needed');

    $request->refresh();

    expect($request->status)->toBe(AssignmentRequest::STATUS_REJECTED)
        ->and($request->decided_by)->toBe($admin->id)
        ->and($schedule->auditLogs()->where('action', 'assignment_request_rejected')->exists())->toBeTrue();
});
