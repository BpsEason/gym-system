<?php

return [
    'fixed_rates' => [
        'junior' => env('SALARY_FIXED_JUNIOR', 30000),
        'mid' => env('SALARY_FIXED_MID', 45000),
        'senior' => env('SALARY_FIXED_SENIOR', 60000),
        'default' => env('SALARY_FIXED_JUNIOR', 30000), # Default if level not found
    ],
    'per_class_rate' => env('SALARY_PER_CLASS_RATE', 500),
];
