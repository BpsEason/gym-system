<?php

return [
    'cancellation_window_hours' => env('COURSE_CANCELLATION_WINDOW_HOURS', 24),
    'default_max_capacity' => env('COURSE_DEFAULT_MAX_CAPACITY', 20),
    'waitlist_notification_timeout_hours' => env('COURSE_WAITLIST_NOTIFICATION_TIMEOUT_HOURS', 2), # How long a waitlist offer is valid
];
