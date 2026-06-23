<?php

declare(strict_types=1);

function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function admin_credentials(): array
{
    $config = db_config();
    return $config['admin'] ?? [];
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
