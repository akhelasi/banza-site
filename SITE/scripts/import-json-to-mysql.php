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

$options = getopt('', ['dry-run', 'only::']);
$dryRun = array_key_exists('dry-run', $options);
$only = (string) ($options['only'] ?? 'contact_messages');

if (!in_array($only, ['contact_messages'], true)) {
    fwrite(STDERR, "Unsupported import target: {$only}\n");
    fwrite(STDERR, "Supported targets: contact_messages\n");
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

if ($dryRun) {
    fwrite(STDOUT, "Dry run OK. Importable contact messages: " . count($importableMessages) . "\n");
    exit(0);
}

$pdo = db();
$pdo->beginTransaction();

try {
    $count = import_contact_messages_to_mysql($pdo, $importableMessages);
    $pdo->commit();
    fwrite(STDOUT, "Imported contact messages: {$count}\n");
} catch (Throwable $exception) {
    $pdo->rollBack();
    fwrite(STDERR, "Import failed: " . $exception->getMessage() . "\n");
    exit(1);
}
