<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lock Window
    |--------------------------------------------------------------------------
    |
    | Schedules become locked for non-admin users this many minutes before
    | their start time. Admins can still override assignments when locked.
    |
    */
    'lock_window_minutes' => (int) env('SCHEDULER_LOCK_MINUTES', 30),
];
