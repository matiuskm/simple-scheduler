<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\ScheduleCalendarController;

Route::get('/', function () {
    return redirect('admin/login');
});

Route::get('/schedules/{schedule}/calendar.ics', [ScheduleCalendarController::class, 'download'])
    ->middleware('auth')
    ->name('schedules.calendar.ics');

Route::get('/jadwal/{schedule:uuid}', [PublicScheduleController::class, 'show'])
    ->name('schedules.public');
