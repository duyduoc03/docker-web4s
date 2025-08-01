<?php

$debug = false;
if (defined('CLIENT_DEBUG')) {
    $debug = (bool)CLIENT_DEBUG;
}

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', '');
}

return [
    'debug' => true,
    'Datasources' => [
        'default' => [
            'host' => DB_HOST,
            'username' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'database' => DB_NAME,
            'url' => env('DATABASE_URL', null),
            'encoding' => 'utf8mb4'
        ]
    ],
    'Security' => [
        'salt' => env('SECURITY_SALT', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'),
    ],
    'DebugKit' => [
        'forceEnable' => true
    ]
];
