<?php

declare(strict_types=1);

function content_storage_path(): string
{
    return dirname(__DIR__) . '/storage/content.json';
}

function content_storage_defaults(array $seed): array
{
    return [
        'news' => $seed['news'] ?? [],
        'projects' => $seed['projects'] ?? [],
        'about' => $seed['about'] ?? [],
        'history' => $seed['history'] ?? [],
        'football' => $seed['football'] ?? [],
        'contact' => $seed['contact'] ?? [],
        'socialLinks' => $seed['socialLinks'] ?? [],
        'bankAccounts' => $seed['bankAccounts'] ?? [],
        'camera' => $seed['camera'] ?? [],
        'weather' => $seed['weather'] ?? [],
    ];
}

function load_content_store(array $seed): array
{
    $defaults = content_storage_defaults($seed);
    $path = content_storage_path();

    if (!is_file($path)) {
        return $defaults;
    }

    $json = file_get_contents($path);
    if ($json === false || trim($json) === '') {
        return $defaults;
    }

    $stored = json_decode($json, true);
    if (!is_array($stored)) {
        return $defaults;
    }

    return array_replace_recursive($defaults, $stored);
}

function normalize_content_asset_paths(mixed $value): mixed
{
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = normalize_content_asset_paths($item);
        }
        return $value;
    }

    if (!is_string($value)) {
        return $value;
    }

    return str_replace('../assets/', 'assets/', $value);
}

function save_content_store(array $content): bool
{
    $path = content_storage_path();
    $directory = dirname($path);

    if (!is_dir($directory) && !mkdir($directory, 0775, true)) {
        return false;
    }

    $normalized = normalize_content_asset_paths($content);
    $json = json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (!is_string($json)) {
        return false;
    }

    return file_put_contents($path, $json . PHP_EOL, LOCK_EX) !== false;
}

function visible_content_items(array $items): array
{
    return array_values(array_filter($items, static function (array $item): bool {
        return empty($item['deleted_at']);
    }));
}

function generate_slug(string $title, string $fallbackPrefix = 'item'): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? '';
    $slug = trim($slug, '-');

    if ($slug === '') {
        $slug = $fallbackPrefix . '-' . date('Ymd-His');
    }

    return substr($slug, 0, 150);
}

function validate_slug(string $slug): bool
{
    return preg_match('/^[a-z0-9][a-z0-9-]{1,158}[a-z0-9]$/', $slug) === 1;
}

function split_lines(string $value): array
{
    $lines = preg_split('/\R+/', $value) ?: [];
    return array_values(array_filter(array_map('trim', $lines), static fn (string $line): bool => $line !== ''));
}