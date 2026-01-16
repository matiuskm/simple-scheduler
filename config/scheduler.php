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

    /*
    |--------------------------------------------------------------------------
    | Email Notifications
    |--------------------------------------------------------------------------
    |
    | When enabled, the app sends minimal email notifications:
    | - assignment confirmation
    | - forced release
    | - schedule cancellation
    |
    */
    'email_notifications' => (bool) env('SCHEDULER_EMAIL_NOTIFICATIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Announcement Banner Cache
    |--------------------------------------------------------------------------
    |
    | Cache the active announcement banner for this many seconds.
    |
    */
    'announcement_cache_seconds' => (int) env('SCHEDULER_ANNOUNCEMENT_CACHE_SECONDS', 60),
];
