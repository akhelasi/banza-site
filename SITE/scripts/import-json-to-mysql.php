<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(1);
}

require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/content-store.php';
require __DIR__ . '/../includes/repositories/contact-message-repository.php';
require __DIR__ . '/../includes/repositories/content-import-repository.php';

$options = getopt('', ['dry-run', 'only::']);
$dryRun = array_key_exists('dry-run', $options);
$only = (string) ($options['only'] ?? 'all');
$supportedTargets = ['all', 'pages', 'posts', 'settings', 'social_links', 'donation_accounts', 'media_items', 'contact_messages'];

if (!in_array($only, $supportedTargets, true)) {
    fwrite(STDERR, "Unsupported import target: {$only}\n");
    fwrite(STDERR, "Supported targets: " . implode(', ', $supportedTargets) . "\n");
    exit(1);
}

$path = content_storage_path();
if (!is_file($path)) {
    fwrite(STDERR, "Content storage file was not found: {$path}\n");
    exit(1);
}

$json = file_get_contents($path);
if ($json === false) {
    fwrite(STDERR, "Could not read content storage file.\n");
    exit(1);
}

$content = json_decode($json, true);
if (!is_array($content)) {
    fwrite(STDERR, "Content storage JSON is invalid: " . json_last_error_msg() . "\n");
    exit(1);
}

if ($dryRun) {
    $summary = content_import_summary($content);
    $targets = $only === 'all' ? array_keys($summary) : [$only];
    fwrite(STDOUT, "Dry run OK.\n");
    foreach ($targets as $target) {
        fwrite(STDOUT, "{$target}: " . ($summary[$target] ?? 0) . "\n");
    }
    exit(0);
}

$pdo = db();
$pdo->beginTransaction();

try {
    $results = [];

    if ($only === 'all' || $only === 'pages') {
        $results['pages'] = import_pages_to_mysql($pdo, content_import_static_pages($content));
    }

    if ($only === 'all' || $only === 'posts') {
        $postResults = import_posts_to_mysql($pdo, content_import_posts($content));
        $results['posts'] = $postResults['posts'];
        $results['media'] = $postResults['media'];
    }

    if ($only === 'all' || $only === 'settings') {
        $results['settings'] = import_settings_to_mysql($pdo, content_import_settings($content));
    }

    if ($only === 'all' || $only === 'social_links') {
        $results['social_links'] = import_social_links_to_mysql($pdo, content_import_social_links($content));
    }

    if ($only === 'all' || $only === 'donation_accounts') {
        $results['donation_accounts'] = import_donation_accounts_to_mysql($pdo, content_import_donation_accounts($content));
    }

    if ($only === 'all' || $only === 'media_items') {
        $results['media_items'] = import_media_items_to_mysql($pdo, content_import_media_items($content));
    }

    if ($only === 'all' || $only === 'contact_messages') {
        $messages = is_array($content['contactMessages'] ?? null) ? $content['contactMessages'] : [];
        $importableMessages = array_values(array_filter($messages, static function (mixed $message): bool {
            if (!is_array($message)) {
                return false;
            }

            return trim((string) ($message['name'] ?? '')) !== ''
                && trim((string) ($message['email'] ?? '')) !== ''
                && trim((string) ($message['subject'] ?? '')) !== ''
                && trim((string) ($message['message'] ?? '')) !== '';
        }));
        $results['contact_messages'] = import_contact_messages_to_mysql($pdo, $importableMessages);
    }

    $pdo->commit();
    foreach ($results as $target => $count) {
        fwrite(STDOUT, "Imported {$target}: {$count}\n");
    }
} catch (Throwable $exception) {
    $pdo->rollBack();
    fwrite(STDERR, "Import failed: " . $exception->getMessage() . "\n");
    exit(1);
}
