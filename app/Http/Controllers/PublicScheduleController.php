<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Contracts\View\View;

class PublicScheduleController extends Controller
{
    public function show(Schedule $schedule): View
    {
        abort_if($schedule->status === Schedule::STATUS_DRAFT, 404);

        $schedule->load(['location', 'users']);

        return view('public-schedule', ['schedule' => $schedule]);
    }
}
