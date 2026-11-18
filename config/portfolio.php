<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Seeded administrator (local / staging only)
    |--------------------------------------------------------------------------
    |
    | When both email and password are present, `php artisan db:seed` creates
    | an administrator. Never set these in production; use
    | `php artisan portfolio:make-admin` there instead.
    |
    */

    'seed_admin' => [
        'name' => env('ADMIN_NAME') ?: 'Administrator',
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact form spam protection
    |--------------------------------------------------------------------------
    |
    | Submissions completed faster than this many seconds after the form was
    | rendered are treated as automated and silently discarded.
    |
    */

    'contact_min_seconds' => (int) env('CONTACT_MIN_SECONDS', 3),

];
