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

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

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
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

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