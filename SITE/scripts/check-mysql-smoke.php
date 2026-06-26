<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(1);
}

require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/repositories/settings-repository.php';
require __DIR__ . '/../includes/repositories/page-repository.php';
require __DIR__ . '/../includes/repositories/post-repository.php';

$options = getopt('', ['admin-email::', 'strict', 'allow-json-driver', 'help']);

if (array_key_exists('help', $options)) {
    fwrite(STDOUT, "Usage: php SITE/scripts/check-mysql-smoke.php [--admin-email=admin@example.com] [--strict] [--allow-json-driver]\n\n");
    fwrite(STDOUT, "Checks a configured MySQL deployment after schema/import/setup have been run.\n");
    fwrite(STDOUT, "Default exit code fails on BLOCKER items. --strict also fails on WARN items.\n");
    exit(0);
}

$strict = array_key_exists('strict', $options);
$allowJsonDriver = array_key_exists('allow-json-driver', $options);
$adminEmail = strtolower(trim((string) ($options['admin-email'] ?? '')));
$checks = [];

function smoke_add(array &$checks, string $level, string $key, string $message): void
{
    $checks[] = ['level' => $level, 'key' => $key, 'message' => $message];
}

function smoke_scalar_count(PDO $pdo, string $sql, array $params = []): int
{
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return (int) $statement->fetchColumn();
}

function smoke_table_exists(PDO $pdo, string $table): bool
{
    $statement = $pdo->prepare('SHOW TABLES LIKE :table_name');
    $statement->execute(['table_name' => $table]);
    return $statement->fetchColumn() !== false;
}

function smoke_require_min_count(array &$checks, PDO $pdo, string $key, string $label, string $sql, int $minimum, array $params = []): void
{
    $count = smoke_scalar_count($pdo, $sql, $params);
    if ($count < $minimum) {
        smoke_add($checks, 'BLOCKER', $key, $label . ' count is ' . $count . ', expected at least ' . $minimum . '.');
        return;
    }

    smoke_add($checks, 'OK', $key, $label . ' count is ' . $count . '.');
}

$driver = content_storage_driver();
if ($driver === 'mysql') {
    smoke_add($checks, 'OK', 'config.storage_driver', 'content_storage.driver is mysql.');
} elseif ($allowJsonDriver) {
    smoke_add($checks, 'WARN', 'config.storage_driver', 'content_storage.driver is ' . $driver . '; continuing because --allow-json-driver was passed.');
} else {
    smoke_add($checks, 'BLOCKER', 'config.storage_driver', 'content_storage.driver is ' . $driver . '; set it to mysql before production smoke testing.');
}

$pdo = null;
try {
    $pdo = db();
    $pdo->query('SELECT 1');
    smoke_add($checks, 'OK', 'mysql.connection', 'PDO connection succeeded.');
} catch (Throwable $exception) {
    smoke_add($checks, 'BLOCKER', 'mysql.connection', 'PDO connection failed: ' . $exception->getMessage());
}

$expectedTables = ['admins', 'pages', 'posts', 'media', 'settings', 'social_links', 'donation_accounts', 'contact_messages'];
$tablesReady = $pdo instanceof PDO;
if ($pdo instanceof PDO) {
    foreach ($expectedTables as $table) {
        if (smoke_table_exists($pdo, $table)) {
            smoke_add($checks, 'OK', 'schema.' . $table, $table . ' table exists.');
        } else {
            $tablesReady = false;
            smoke_add($checks, 'BLOCKER', 'schema.' . $table, $table . ' table is missing.');
        }
    }
}

if ($pdo instanceof PDO && $tablesReady) {
    smoke_require_min_count($checks, $pdo, 'data.admins', 'Active admin rows', 'SELECT COUNT(*) FROM admins WHERE deleted_at IS NULL', 1);
    smoke_require_min_count($checks, $pdo, 'data.pages', 'Published page rows', "SELECT COUNT(*) FROM pages WHERE status = 'published' AND deleted_at IS NULL", 4);
    smoke_require_min_count($checks, $pdo, 'data.news', 'Published news rows', "SELECT COUNT(*) FROM posts WHERE type = 'news' AND status = 'published' AND deleted_at IS NULL", 1);
    smoke_require_min_count($checks, $pdo, 'data.projects', 'Published project rows', "SELECT COUNT(*) FROM posts WHERE type = 'project' AND status = 'published' AND deleted_at IS NULL", 1);
    smoke_require_min_count($checks, $pdo, 'data.settings', 'Settings rows', 'SELECT COUNT(*) FROM settings', 3);
    smoke_require_min_count($checks, $pdo, 'data.social_links', 'Active social link rows', 'SELECT COUNT(*) FROM social_links WHERE deleted_at IS NULL', 1);
    smoke_require_min_count($checks, $pdo, 'data.donation_accounts', 'Active donation account rows', 'SELECT COUNT(*) FROM donation_accounts WHERE deleted_at IS NULL', 1);

    $mediaCount = smoke_scalar_count($pdo, 'SELECT COUNT(*) FROM media WHERE deleted_at IS NULL');
    smoke_add($checks, $mediaCount > 0 ? 'OK' : 'WARN', 'data.media', 'Active media rows: ' . $mediaCount . '.');

    $messageCount = smoke_scalar_count($pdo, 'SELECT COUNT(*) FROM contact_messages WHERE deleted_at IS NULL');
    smoke_add($checks, 'OK', 'data.contact_messages', 'Active contact message rows: ' . $messageCount . '.');

    if ($adminEmail !== '') {
        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            smoke_add($checks, 'BLOCKER', 'admin.email', '--admin-email is not a valid email address.');
        } else {
            $adminCount = smoke_scalar_count($pdo, 'SELECT COUNT(*) FROM admins WHERE email = :email AND deleted_at IS NULL', ['email' => $adminEmail]);
            smoke_add($checks, $adminCount > 0 ? 'OK' : 'BLOCKER', 'admin.email', $adminCount > 0 ? 'Admin email exists: ' . $adminEmail : 'Admin email not found: ' . $adminEmail);
        }
    } else {
        smoke_add($checks, 'WARN', 'admin.email', 'Pass --admin-email to verify the exact production admin row.');
    }

    try {
        $settings = load_runtime_settings_from_mysql($pdo);
        $pages = load_runtime_pages_from_mysql($pdo);
        $posts = load_runtime_posts_from_mysql($pdo);

        smoke_add($checks, !empty($settings['socialLinks']) ? 'OK' : 'BLOCKER', 'runtime.social_links', 'Runtime social links loaded: ' . count($settings['socialLinks'] ?? []) . '.');
        smoke_add($checks, !empty($settings['bankAccounts']) ? 'OK' : 'BLOCKER', 'runtime.donation_accounts', 'Runtime donation accounts loaded: ' . count($settings['bankAccounts'] ?? []) . '.');

        foreach (['about', 'history', 'football', 'contact'] as $pageKey) {
            smoke_add($checks, !empty($pages[$pageKey]) ? 'OK' : 'BLOCKER', 'runtime.page.' . $pageKey, !empty($pages[$pageKey]) ? 'Runtime page loaded: ' . $pageKey . '.' : 'Runtime page missing: ' . $pageKey . '.');
        }

        smoke_add($checks, !empty($posts['news']) ? 'OK' : 'BLOCKER', 'runtime.news', 'Runtime news loaded: ' . count($posts['news'] ?? []) . '.');
        smoke_add($checks, !empty($posts['projects']) ? 'OK' : 'BLOCKER', 'runtime.projects', 'Runtime projects loaded: ' . count($posts['projects'] ?? []) . '.');
    } catch (Throwable $exception) {
        smoke_add($checks, 'BLOCKER', 'runtime.repositories', 'Runtime repository read failed: ' . $exception->getMessage());
    }
}

$rank = ['BLOCKER' => 0, 'WARN' => 1, 'OK' => 2];
usort($checks, static function (array $a, array $b) use ($rank): int {
    return ($rank[$a['level']] ?? 9) <=> ($rank[$b['level']] ?? 9) ?: strcmp($a['key'], $b['key']);
});

$counts = ['OK' => 0, 'WARN' => 0, 'BLOCKER' => 0];
foreach ($checks as $check) {
    $counts[$check['level']] = ($counts[$check['level']] ?? 0) + 1;
}

fwrite(STDOUT, "MySQL smoke check\n");
fwrite(STDOUT, 'OK: ' . $counts['OK'] . ' | WARN: ' . $counts['WARN'] . ' | BLOCKER: ' . $counts['BLOCKER'] . PHP_EOL);

foreach ($checks as $check) {
    fwrite(STDOUT, '[' . $check['level'] . '] ' . $check['key'] . ': ' . $check['message'] . PHP_EOL);
}

if ($counts['BLOCKER'] > 0) {
    exit(1);
}

if ($strict && $counts['WARN'] > 0) {
    exit(1);
}

exit(0);