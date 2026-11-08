<?php

use App\Models\Schedule;
use App\Models\User;

it('shows a public schedule by uuid without login', function () {
    $schedule = Schedule::factory()->create(['title' => 'Misa Minggu']);
    $schedule->users()->attach(User::factory()->create(['name' => 'Budi Santoso']));

    $this->get(route('schedules.public', $schedule))
        ->assertOk()
        ->assertSee('Misa Minggu')
        ->assertSee('Budi Santoso');
});

it('returns 404 for unknown uuid and draft schedules', function () {
    $this->get('/jadwal/'.fake()->uuid())->assertNotFound();

    $draft = Schedule::factory()->draft()->create();
    $this->get(route('schedules.public', $draft))->assertNotFound();
});

it('assigns a uuid on creation', function () {
    expect(Schedule::factory()->create()->uuid)->not->toBeEmpty();
});
