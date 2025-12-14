<?php

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;

it('upcomingVisible includes today and excludes cancelled/completed', function () {
    Carbon::setTestNow(now());

    $todayOpen = Schedule::factory()->create([
        'scheduled_date' => today(),
        'status' => Schedule::STATUS_PUBLISHED,
    ]);

    $cancelled = Schedule::factory()->create([
        'scheduled_date' => today(),
        'status' => Schedule::STATUS_CANCELLED,
    ]);

    $futureCompleted = Schedule::factory()->create([
        'scheduled_date' => today()->addDay(),
        'status' => Schedule::STATUS_COMPLETED,
    ]);

    $visible = Schedule::upcomingVisible()->pluck('id')->all();

    expect($visible)->toContain($todayOpen->id)
        ->and($visible)->not()->toContain($cancelled->id)
        ->and($visible)->not()->toContain($futureCompleted->id);
});

it('needingPersonnel ignores cancelled/completed schedules', function () {
    $schedule = Schedule::factory()->create([
        'required_personnel' => 2,
        'status' => Schedule::STATUS_PUBLISHED,
    ]);

    $cancelled = Schedule::factory()->create([
        'required_personnel' => 2,
        'status' => Schedule::STATUS_CANCELLED,
    ]);

    $user = User::factory()->create();
    $cancelled->users()->attach($user->id);

    $needs = Schedule::needingPersonnel()->pluck('id')->all();

    expect($needs)->toContain($schedule->id)
        ->and($needs)->not()->toContain($cancelled->id);
});
