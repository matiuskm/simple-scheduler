<?php

use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

it('filters active announcements by time window and enabled flag', function () {
    Carbon::setTestNow(now());

    $active = Announcement::factory()->create([
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
        'is_enabled' => true,
    ]);

    $disabled = Announcement::factory()->create([
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
        'is_enabled' => false,
    ]);

    $future = Announcement::factory()->create([
        'start_at' => now()->addHour(),
        'end_at' => now()->addHours(2),
        'is_enabled' => true,
    ]);

    $past = Announcement::factory()->create([
        'start_at' => now()->subHours(2),
        'end_at' => now()->subHour(),
        'is_enabled' => true,
    ]);

    $ids = Announcement::activeNow()->pluck('id')->all();

    expect($ids)->toContain($active->id)
        ->and($ids)->not()->toContain($disabled->id)
        ->and($ids)->not()->toContain($future->id)
        ->and($ids)->not()->toContain($past->id);
});

it('selects the latest active announcement for cached banner', function () {
    Carbon::setTestNow(now());
    Cache::forget(Announcement::CACHE_KEY_ACTIVE);

    $older = Announcement::factory()->create([
        'start_at' => now()->subMinutes(30),
        'end_at' => now()->addHour(),
    ]);

    $latest = Announcement::factory()->create([
        'start_at' => now()->subMinutes(5),
        'end_at' => now()->addHour(),
    ]);

    expect(Announcement::activeCached()?->id)->toBe($latest->id);
});

it('returns a list of active announcements for stacking', function () {
    Carbon::setTestNow(now());
    Cache::forget(Announcement::CACHE_KEY_ACTIVE . '.list.3');

    $first = Announcement::factory()->create([
        'start_at' => now()->subMinutes(20),
        'end_at' => now()->addHour(),
    ]);

    $second = Announcement::factory()->create([
        'start_at' => now()->subMinutes(10),
        'end_at' => now()->addHour(),
    ]);

    $third = Announcement::factory()->create([
        'start_at' => now()->subMinutes(5),
        'end_at' => now()->addHour(),
    ]);

    $list = Announcement::activeListCached(3);

    expect($list)->toHaveCount(3)
        ->and($list->first()->id)->toBe($third->id)
        ->and($list->last()->id)->toBe($first->id);
});
