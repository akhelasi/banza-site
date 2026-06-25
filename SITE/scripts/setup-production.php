<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(1);
}

function setup_usage(): string
{
    return implode(PHP_EOL, [
        'Usage:',
        '  php SITE/scripts/setup-production.php --email=admin@example.com --password="strong password" [--name="Banza Admin"] [--dry-run] [--force] [--skip-defaults]',
        '  php SITE/scripts/setup-production.php --email=admin@example.com --password-env=BANZA_ADMIN_PASSWORD [--name="Banza Admin"] [--dry-run] [--force]',
        '  php SITE/scripts/setup-production.php --migrate [--dry-run]',
        '  php SITE/scripts/setup-production.php --check-routes',
        '  php SITE/scripts/setup-production.php --audit-content [--allow-open]',
        '',
        'Options:',
        '  --email           Required admin email.',
        '  --password        Required unless --password-env is used. Minimum 12 characters.',
        '  --password-env    Read password from an environment variable to avoid shell history.',
        '  --name            Admin display name. Default: Banza Admin.',
        '  --dry-run         Validate input and show planned seed counts without connecting to MySQL.',
        '  --force           Update the existing admin row when the email already exists.',
        '  --skip-defaults   Create/update admin only; do not seed settings/social/donation defaults.',
        '  --migrate         Run SQL migration files from SITE/database/migrations in filename order.',
        '  --check-routes    Render major public routes and fail on PHP warnings/notices or missing expected markup.',
        '  --audit-content   Check launch content blockers such as demo text, generic links/accounts and unapproved source statuses.',
        '  --allow-open      Report audit blockers without failing the command.',
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

function setup_migration_files(): array
{
    $paths = glob(dirname(__DIR__) . '/database/migrations/*.sql') ?: [];
    sort($paths, SORT_STRING);

    return $paths;
}

function setup_sql_statements(string $sql): array
{
    $parts = explode(';', $sql);

    return array_values(array_filter(array_map('trim', $parts), static fn (string $statement): bool => $statement !== ''));
}

function setup_run_migrations(bool $dryRun): int
{
    $files = setup_migration_files();
    if ($files === []) {
        fwrite(STDOUT, "No migration files found.\n");

        return 0;
    }

    fwrite(STDOUT, "Migration files:\n");
    foreach ($files as $file) {
        fwrite(STDOUT, '- ' . basename($file) . PHP_EOL);
    }

    if ($dryRun) {
        fwrite(STDOUT, "Dry run OK. MySQL connection was not opened.\n");

        return 0;
    }

    require __DIR__ . '/../includes/database.php';

    $pdo = db();
    foreach ($files as $file) {
        $sql = file_get_contents($file);
        if ($sql === false) {
            fwrite(STDERR, 'Could not read migration: ' . basename($file) . PHP_EOL);

            return 1;
        }

        foreach (setup_sql_statements($sql) as $statement) {
            $pdo->exec($statement);
        }

        fwrite(STDOUT, 'Applied: ' . basename($file) . PHP_EOL);
    }

    fwrite(STDOUT, "Migrations complete.\n");

    return 0;
}


function setup_text_contains_demo(mixed $value): bool
{
    $text = mb_strtolower(plain_text($value));

    return str_contains($text, 'demo') || str_contains($text, 'placeholder');
}

function setup_add_audit_issue(array &$issues, string $level, string $where, string $message): void
{
    $issues[] = compact('level', 'where', 'message');
}

function setup_audit_content_item(array &$issues, string $where, array $item): void
{
    $sourceStatus = (string) ($item['source_status'] ?? '');
    $sourceNote = trim((string) ($item['source_note'] ?? ''));

    if ($sourceStatus !== 'client_approved') {
        setup_add_audit_issue($issues, 'BLOCKER', $where, 'Content is not client_approved.');
    }

    if ($sourceStatus !== 'client_approved' && $sourceNote === '') {
        setup_add_audit_issue($issues, 'BLOCKER', $where, 'Missing source_note for non-approved content.');
    }

    if (setup_text_contains_demo($item)) {
        setup_add_audit_issue($issues, 'BLOCKER', $where, 'Contains demo/placeholder wording.');
    }

    $image = (string) ($item['image'] ?? $item['hero_image'] ?? '');
    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
        setup_add_audit_issue($issues, 'REVIEW', $where, 'Uses a remote image URL; replace with approved local asset if needed.');
    }
}

function setup_audit_launch_content(bool $allowOpen): int
{
    require __DIR__ . '/../includes/helpers.php';
    require __DIR__ . '/../includes/data.php';

    $issues = [];
    foreach ($news as $item) {
        setup_audit_content_item($issues, 'news:' . ($item['slug'] ?? 'unknown'), $item);
    }

    foreach ($projects as $item) {
        setup_audit_content_item($issues, 'project:' . ($item['slug'] ?? 'unknown'), $item);
    }

    foreach (['about' => $about, 'history' => $history, 'football' => $football, 'contact' => $contact] as $key => $item) {
        setup_audit_content_item($issues, 'page:' . $key, is_array($item) ? $item : []);
    }

    foreach ($bankAccounts as $index => $account) {
        $where = 'donation_account:' . ($index + 1);
        if (setup_text_contains_demo($account)) {
            setup_add_audit_issue($issues, 'BLOCKER', $where, 'Donation account contains demo wording.');
        }

        $accountNumber = (string) ($account['account'] ?? '');
        if (str_starts_with($accountNumber, 'GE00')) {
            setup_add_audit_issue($issues, 'BLOCKER', $where, 'Donation account looks like a placeholder IBAN.');
        }
    }

    foreach ($socialLinks as $link) {
        $where = 'social:' . ($link['label'] ?? 'unknown');
        $href = rtrim((string) ($link['href'] ?? ''), '/');
        if (in_array($href, ['https://facebook.com', 'https://instagram.com', 'https://youtube.com'], true)) {
            setup_add_audit_issue($issues, 'BLOCKER', $where, 'Social link still points to a generic platform URL.');
        }
    }

    foreach (($contact['items'] ?? []) as $index => $item) {
        $where = 'contact_item:' . ($index + 1);
        if (setup_text_contains_demo($item)) {
            setup_add_audit_issue($issues, 'BLOCKER', $where, 'Contact item contains demo wording.');
        }

        $value = (string) ($item['value'] ?? '');
        if ($value === 'info@banza.ge' || str_contains($value, '+995 000')) {
            setup_add_audit_issue($issues, 'BLOCKER', $where, 'Contact item looks like a placeholder value.');
        }
    }

    $blockers = array_values(array_filter($issues, static fn (array $issue): bool => $issue['level'] === 'BLOCKER'));
    $reviews = array_values(array_filter($issues, static fn (array $issue): bool => $issue['level'] === 'REVIEW'));

    fwrite(STDOUT, "Content audit complete.\n");
    fwrite(STDOUT, 'Blockers: ' . count($blockers) . PHP_EOL);
    fwrite(STDOUT, 'Review items: ' . count($reviews) . PHP_EOL);

    foreach ($issues as $issue) {
        fwrite(STDOUT, "[{$issue['level']}] {$issue['where']}: {$issue['message']}\n");
    }

    if ($blockers !== [] && !$allowOpen) {
        fwrite(STDERR, "Launch content blockers remain. Re-run with --allow-open for handoff/reporting mode.\n");

        return 1;
    }

    return 0;
}

function setup_render_route(string $file, array $get, string $needle): int
{
    $siteRoot = dirname(__DIR__);
    $allowedFiles = ['index.php', 'news.php', 'projects.php', 'about.php', 'history.php', 'football.php', 'contact.php', 'news-detail.php', 'project-detail.php'];
    if (!in_array($file, $allowedFiles, true)) {
        fwrite(STDERR, "Unsupported file\n");

        return 1;
    }

    chdir($siteRoot);
    ini_set('session.save_path', sys_get_temp_dir());
    set_error_handler(static function (int $severity, string $message, string $filePath, int $line): bool {
        if ((error_reporting() & $severity) === 0) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $filePath, $line);
    });

    $_GET = $get;
    $_POST = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/' . $file;

    ob_start();
    include $siteRoot . '/' . $file;
    $html = (string) ob_get_clean();

    if ($needle !== '' && !str_contains($html, $needle)) {
        fwrite(STDERR, $file . " missing {$needle}\n");

        return 1;
    }

    fwrite(STDOUT, $file . " ok\n");

    return 0;
}

function setup_check_public_routes(): int
{
    $routes = [
        ['file' => 'index.php', 'get' => [], 'contains' => 'main-content'],
        ['file' => 'news.php', 'get' => [], 'contains' => 'main-content'],
        ['file' => 'projects.php', 'get' => [], 'contains' => 'main-content'],
        ['file' => 'about.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'history.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'football.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'contact.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'news-detail.php', 'get' => ['slug' => 'history-archive-seed'], 'contains' => 'source-note'],
        ['file' => 'project-detail.php', 'get' => ['slug' => 'village-development-roadmap'], 'contains' => 'source-note'],
    ];

    foreach ($routes as $route) {
        $command = escapeshellarg(PHP_BINARY)
            . ' ' . escapeshellarg(__FILE__)
            . ' --file=' . escapeshellarg($route['file'])
            . ' --get=' . escapeshellarg(json_encode($route['get'], JSON_UNESCAPED_SLASHES) ?: '{}')
            . ' --contains=' . escapeshellarg($route['contains'])
            . ' 2>&1';

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);
        echo implode(PHP_EOL, $output) . PHP_EOL;

        if ($exitCode !== 0) {
            return $exitCode;
        }
    }

    return 0;
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
    'migrate',
    'check-routes',
    'audit-content',
    'allow-open',
    'file::',
    'get::',
    'contains::',
    'help',
]);

if (array_key_exists('help', $options)) {
    fwrite(STDOUT, setup_usage());
    exit(0);
}
if (array_key_exists('audit-content', $options)) {
    exit(setup_audit_launch_content(array_key_exists('allow-open', $options)));
}

if (array_key_exists('check-routes', $options)) {
    exit(setup_check_public_routes());
}

if (array_key_exists('file', $options)) {
    $get = json_decode((string) ($options['get'] ?? '{}'), true);
    if (!is_array($get)) {
        $get = [];
    }

    exit(setup_render_route((string) $options['file'], $get, (string) ($options['contains'] ?? 'main-content')));
}

if (array_key_exists('migrate', $options)) {
    exit(setup_run_migrations(array_key_exists('dry-run', $options)));
}

require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/content-store.php';
require __DIR__ . '/../includes/repositories/content-import-repository.php';
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
    fwrite(STDOUT, "Note: when content_storage.driver=mysql, admin login reads the admins table with config fallback.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, "Setup failed: " . $exception->getMessage() . "\n");
    exit(1);
}
