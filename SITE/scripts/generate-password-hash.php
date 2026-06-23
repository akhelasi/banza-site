<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$password = (string) ($argv[1] ?? '');

if ($password === '') {
    fwrite(STDERR, "Usage: php SITE/scripts/generate-password-hash.php \"new strong password\"\n");
    exit(1);
}

if (strlen($password) < 12) {
    fwrite(STDERR, "Password must be at least 12 characters.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

echo $hash . PHP_EOL;
echo "Copy this value into SITE/includes/config.php as admin.password_hash." . PHP_EOL;