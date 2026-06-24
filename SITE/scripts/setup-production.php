<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(1);
}

require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/content-store.php';
require __DIR__ . '/../includes/repositories/content-import-repository.php';

function setup_usage(): string
{
    return implode(PHP_EOL, [
        'Usage:',
        '  php SITE/scripts/setup-production.php --email=admin@example.com --password="strong password" [--name="Banza Admin"] [--dry-run] [--force] [--skip-defaults]',
        '  php SITE/scripts/setup-production.php --email=admin@example.com --password-env=BANZA_ADMIN_PASSWORD [--name="Banza Admin"] [--dry-run] [--force]',
        '',
        'Options:',
        '  --email           Required admin email.',
        '  --password        Required unless --password-env is used. Minimum 12 characters.',
        '  --password-env    Read password from an environment variable to avoid shell history.',
        '  --name            Admin display name. Default: Banza Admin.',
        '  --dry-run         Validate input and show planned seed counts without connecting to MySQL.',
        '  --force           Update the existing admin row when the email already exists.',
        '  --skip-defaults   Create/update admin only; do not seed settings/social/donation defaults.',
    ]) . PHP_EOL;
}

function setup_password_from_options(array $options): string
{
    $password = (string) ($options['password'] ?? '');
    $passwordEnv = trim((string) ($options['password-env'] ?? ''));

    if ($password === '' && $passwordEnv !== '') {
        $envPassword = getenv($passwordEnv);
        $password = is_string($envPassword) ? $envPassword : '';
    }

    return $password;
}

function setup_load_content(): array
{
    $path = content_storage_path();
    if (!is_file($path)) {
        throw new RuntimeException("Content storage file was not found: {$path}");
    }

    $json = file_get_contents($path);
    if ($json === false) {
        throw new RuntimeException('Could not read content storage file.');
    }

    $content = json_decode($json, true);
    if (!is_array($content)) {
        throw new RuntimeException('Content storage JSON is invalid: ' . json_last_error_msg());
    }

    return $content;
}

function setup_seed_admin(PDO $pdo, string $name, string $email, string $passwordHash, bool $force): string
{
    $find = $pdo->prepare('SELECT id FROM admins WHERE email = :email LIMIT 1');
    $find->execute(['email' => $email]);
    $existing = $find->fetch();

    if (is_array($existing)) {
        if (!$force) {
            throw new RuntimeException('Admin email already exists. Re-run with --force to update that admin password/name.');
        }

        $update = $pdo->prepare(
            'UPDATE admins
             SET name = :name, password_hash = :password_hash, deleted_at = NULL
             WHERE id = :id'
        );
        $update->execute([
            'id' => (int) $existing['id'],
            'name' => $name,
            'password_hash' => $passwordHash,
        ]);

        return 'updated';
    }

    $insert = $pdo->prepare(
        'INSERT INTO admins (name, email, password_hash)
         VALUES (:name, :email, :password_hash)'
    );
    $insert->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => $passwordHash,
    ]);

    return 'created';
}

$options = getopt('', [
    'email:',
    'password::',
    'password-env::',
    'name::',
    'dry-run',
    'force',
    'skip-defaults',
    'help',
]);

if (array_key_exists('help', $options)) {
    fwrite(STDOUT, setup_usage());
    exit(0);
}

$email = strtolower(trim((string) ($options['email'] ?? '')));
$name = trim((string) ($options['name'] ?? 'Banza Admin'));
$password = setup_password_from_options($options);
$dryRun = array_key_exists('dry-run', $options);
$force = array_key_exists('force', $options);
$skipDefaults = array_key_exists('skip-defaults', $options);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "A valid --email value is required.\n\n" . setup_usage());
    exit(1);
}

if ($name === '' || mb_strlen($name) > 120) {
    fwrite(STDERR, "--name must be between 1 and 120 characters.\n");
    exit(1);
}

if (strlen($password) < 12) {
    fwrite(STDERR, "Password must be at least 12 characters. Use --password or --password-env.\n");
    exit(1);
}

try {
    $content = setup_load_content();
    $settings = $skipDefaults ? [] : content_import_settings($content);
    $socialLinks = $skipDefaults ? [] : content_import_social_links($content);
    $donationAccounts = $skipDefaults ? [] : content_import_donation_accounts($content);

    if ($dryRun) {
        fwrite(STDOUT, "Dry run OK.\n");
        fwrite(STDOUT, "Admin email: {$email}\n");
        fwrite(STDOUT, "Admin name: {$name}\n");
        fwrite(STDOUT, "Default settings: " . count($settings) . "\n");
        fwrite(STDOUT, "Social links: " . count($socialLinks) . "\n");
        fwrite(STDOUT, "Donation accounts: " . count($donationAccounts) . "\n");
        fwrite(STDOUT, "MySQL connection was not opened in dry-run mode.\n");
        exit(0);
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $adminAction = setup_seed_admin($pdo, $name, $email, password_hash($password, PASSWORD_DEFAULT), $force);
        $seededSettings = $skipDefaults ? 0 : import_settings_to_mysql($pdo, $settings);
        $seededSocialLinks = $skipDefaults ? 0 : import_social_links_to_mysql($pdo, $socialLinks);
        $seededDonationAccounts = $skipDefaults ? 0 : import_donation_accounts_to_mysql($pdo, $donationAccounts);

        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }

    fwrite(STDOUT, "Setup complete.\n");
    fwrite(STDOUT, "Admin {$adminAction}: {$email}\n");
    fwrite(STDOUT, "Default settings seeded: {$seededSettings}\n");
    fwrite(STDOUT, "Social links seeded: {$seededSocialLinks}\n");
    fwrite(STDOUT, "Donation accounts seeded: {$seededDonationAccounts}\n");
    fwrite(STDOUT, "Note: current runtime login still reads SITE/includes/config.php admin credentials until DB-backed auth is enabled.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, "Setup failed: " . $exception->getMessage() . "\n");
    exit(1);
}
