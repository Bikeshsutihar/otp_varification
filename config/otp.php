<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Length
    |--------------------------------------------------------------------------
    | Number of digits in the generated OTP code.
    */
    'length' => env('OTP_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | OTP Expiry (minutes)
    |--------------------------------------------------------------------------
    */
    'expires_in_minutes' => env('OTP_EXPIRES_IN_MINUTES', 10),

    /*
    |--------------------------------------------------------------------------
    | Max Verification Attempts
    |--------------------------------------------------------------------------
    | Once exceeded, the code is invalidated and the user must request a new one.
    */
    'max_attempts' => env('OTP_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Resend Cooldown (seconds)
    |--------------------------------------------------------------------------
    | Minimum time a user must wait before requesting another OTP.
    */
    'resend_cooldown_seconds' => env('OTP_RESEND_COOLDOWN', 60),

];
