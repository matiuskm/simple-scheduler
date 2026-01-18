<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Services\IcsCalendarService;
use Illuminate\Http\Response;

class ScheduleCalendarController extends Controller
{
    public function __construct(private readonly IcsCalendarService $icsCalendarService)
    {
    }

    public function download(Schedule $schedule): Response
    {
        // Usage: GET /schedules/{schedule}/calendar.ics
        $ics = $this->icsCalendarService->makeScheduleIcs($schedule->loadMissing('location'));

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"schedule-{$schedule->id}.ics\"",
        ]);
    }
}
