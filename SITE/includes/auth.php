<?php

declare(strict_types=1);

function ensure_session(): void
{
    if (function_exists('app_session_start')) {
        app_session_start();
        return;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function admin_credentials(): array
{
    $config = db_config();
    return $config['admin'] ?? [];
}

function admin_runtime_config_path(): string
{
    return __DIR__ . '/config.php';
}

function write_admin_credentials(string $email, string $passwordHash): bool
{
    $config = db_config();
    $config['admin'] = is_array($config['admin'] ?? null) ? $config['admin'] : [];
    $config['admin']['email'] = strtolower(trim($email));
    $config['admin']['password_hash'] = $passwordHash;

    $path = admin_runtime_config_path();
    $export = "<?php\nreturn " . var_export($config, true) . ";\n";

    return file_put_contents($path, $export, LOCK_EX) !== false;
}

function is_admin_logged_in(): bool
{
    ensure_session();
    return isset($_SESSION['admin']) && is_array($_SESSION['admin']);
}

function current_admin(): ?array
{
    ensure_session();
    return is_admin_logged_in() ? $_SESSION['admin'] : null;
}

function attempt_admin_login(string $email, string $password): bool
{
    ensure_session();
    $credentials = admin_credentials();
    $expectedEmail = strtolower(trim((string) ($credentials['email'] ?? '')));
    $hash = (string) ($credentials['password_hash'] ?? '');

    if ($expectedEmail === '' || $hash === '') {
        return false;
    }

    if (strtolower(trim($email)) !== $expectedEmail) {
        return false;
    }

    if (!password_verify($password, $hash)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin'] = [
        'email' => $expectedEmail,
        'name' => 'Banza Admin',
        'login_at' => date(DATE_ATOM),
    ];

    return true;
}

function logout_admin(): void
{
    ensure_session();
    unset($_SESSION['admin']);
    session_regenerate_id(true);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php');
        $adminPath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        redirect(($adminPath !== '' ? $adminPath : '/admin') . '/login.php');
    }
}

function admin_flash(?string $message = null, string $type = 'success'): ?array
{
    ensure_session();

    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}


function update_current_admin_session(string $email): void
{
    ensure_session();

    if (!isset($_SESSION['admin']) || !is_array($_SESSION['admin'])) {
        return;
    }

    $_SESSION['admin']['email'] = strtolower(trim($email));
}
