<?php
use App\Core\Env;

return [
    'name'     => Env::get('APP_NAME', 'Maato'),
    'env'      => Env::get('APP_ENV', 'production'),
    'debug'    => Env::bool('APP_DEBUG', false),
    'url'      => rtrim((string) Env::get('APP_URL', 'http://localhost'), '/'),
    'timezone' => Env::get('APP_TIMEZONE', 'Asia/Kathmandu'),
    'session'  => [
        'name'     => 'maato_sid',
        'secure'   => Env::get('SESSION_SECURE', 'auto'),
        'lifetime' => (int) Env::get('SESSION_LIFETIME_MINUTES', 120),
    ],
    'uploads' => [
        'max_bytes' => (int) Env::get('UPLOAD_MAX_MB', 4) * 1024 * 1024,
        'path'      => BASE_PATH . '/storage/uploads',
    ],
    'auth' => [
        'max_attempts'     => 5,   // failures per email+IP inside the window
        'max_ip_attempts'  => 20,  // failures per IP across all emails
        'window_minutes'   => 15,
        'reset_ttl_minutes'=> 30,
    ],
];
