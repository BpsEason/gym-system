<?php

return [
    'rules' => [
        'course_booking' => env('POINTS_COURSE_BOOKING', 5),
        'course_attendance' => env('POINTS_COURSE_ATTENDANCE', 10),
        'payment_per_100' => env('POINTS_PAYMENT_PER_100', 5), # points per 100 currency units spent
        'referral' => env('POINTS_REFERRAL', 50),
    ],
];
