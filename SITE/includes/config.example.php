<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'banza_site',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'admin' => [
        'email' => 'admin@banza.local',
        // Demo password for local scaffold only: AdminDemo2026!
        // Replace this hash in SITE/includes/config.php before production use.
        'password_hash' => '$2y$10$WIhw6opCD.vQ6/r.Wtw6huVNVtCfqHnPlX0wTrBpgPwU0dcJkCUpe',
    ],
    'content_storage' => [
        // Keep json for local development and content/design approval.
        // Switch to mysql only after running schema.sql and import scripts.
        'driver' => 'json',
    ],
    'session' => [
        'name' => 'banza_admin_session',
        // Set to true in SITE/includes/config.php after enabling HTTPS.
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ],
    'security' => [
        'send_headers' => true,
    ],
];
