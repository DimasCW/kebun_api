<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Terapkan aturan ke semua rute API
    'allowed_methods' => ['*'], // Izinkan semua metode (GET, POST, dll)
    'allowed_origins' => ['*'], // Izinkan request dari domain manapun
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Izinkan semua header
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];

?>