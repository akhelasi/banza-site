<?php

declare(strict_types=1);

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function asset(string $path): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $prefix = str_contains($scriptName, '/admin/') ? '../assets/' : 'assets/';
    return $prefix . ltrim($path, '/');
}

function page_url(string $path = ''): string
{
    return $path === '' ? 'index.php' : $path;
}

function is_active_nav(string $current, string $target): string
{
    return $current === $target ? ' aria-current="page"' : '';
}

function app_config_section(string $key): array
{
    if (!function_exists('db_config')) {
        return [];
    }

    $config = db_config();
    return isset($config[$key]) && is_array($config[$key]) ? $config[$key] : [];
}

function is_https_request(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443')
        || (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');
}

function app_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $sessionConfig = app_config_section('session');
    $sessionName = trim((string) ($sessionConfig['name'] ?? 'banza_admin_session'));

    if ($sessionName !== '') {
        session_name($sessionName);
    }

    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => (bool) ($sessionConfig['secure'] ?? is_https_request()),
        'httponly' => (bool) ($sessionConfig['httponly'] ?? true),
        'samesite' => (string) ($sessionConfig['samesite'] ?? 'Lax'),
    ]);
    session_start();
}

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    $securityConfig = app_config_section('security');
    if (($securityConfig['send_headers'] ?? true) === false) {
        return;
    }

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function csrf_token(): string
{
    app_session_start();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    app_session_start();

    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function find_by_slug(array $items, string $slug): ?array
{
    foreach ($items as $item) {
        if (($item['slug'] ?? '') === $slug) {
            return $item;
        }
    }

    return null;
}

function plain_text(array|string|null $content): string
{
    if (is_array($content)) {
        return implode(' ', array_map('plain_text', $content));
    }

    return trim((string) $content);
}
