<?php

use App\Models\Location;
use App\Models\Schedule;
use App\Models\User;

it('returns a valid ics download for a schedule', function () {
    $user = User::factory()->create();
    $location = Location::factory()->create(['name' => 'Gereja X']);

    $schedule = Schedule::factory()->create([
        'scheduled_date' => '2026-01-19',
        'start_time' => '07:00:00',
        'end_time' => '08:30:00',
        'location_id' => $location->id,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('schedules.calendar.ics', $schedule));

    $response->assertOk();

    $contentType = $response->headers->get('content-type');
    expect($contentType)->toContain('text/calendar');

    $response->assertSee('BEGIN:VCALENDAR', false);
    $response->assertSee('BEGIN:VEVENT', false);
    $response->assertSee('DTSTART;TZID=Asia/Jakarta:20260119T070000', false);
});
