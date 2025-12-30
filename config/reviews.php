<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Review Cooldown Period
    |--------------------------------------------------------------------------
    |
    | This option defines the time period (in hours) that must pass before
    | a user can write another review for the same book. By default, it's
    | set to 24 hours. You can change this value at any time.
    |
    */

    'cooldown_hours' => env('REVIEW_COOLDOWN_HOURS', 24),
];

