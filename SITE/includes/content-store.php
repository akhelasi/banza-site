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
        'contactMessages' => $seed['contactMessages'] ?? [],
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

function content_date_today(): string
{
    return date('d/m/Y');
}

function touch_content_dates(array $item, bool $isNew = false): array
{
    if ($isNew || empty($item['post_date'])) {
        $item['post_date'] = content_date_today();
    }

    $item['last_update'] = content_date_today();

    return $item;
}

function collect_upload_paths(mixed $value): array
{
    $paths = [];

    if (is_array($value)) {
        foreach ($value as $item) {
            $paths = array_merge($paths, collect_upload_paths($item));
        }
        return array_values(array_unique($paths));
    }

    if (is_string($value) && str_starts_with($value, 'uploads/')) {
        return [$value];
    }

    return [];
}

function uploaded_path_is_referenced(array $content, string $path): bool
{
    return in_array($path, collect_upload_paths($content), true);
}

function delete_uploaded_file_if_unreferenced(string $path, array $remainingContent): bool
{
    if (!str_starts_with($path, 'uploads/')) {
        return false;
    }

    if (uploaded_path_is_referenced($remainingContent, $path)) {
        return false;
    }

    $absolutePath = dirname(__DIR__) . '/' . $path;
    $uploadsRoot = realpath(dirname(__DIR__) . '/uploads');
    $target = realpath($absolutePath);

    if ($uploadsRoot === false || $target === false || !str_starts_with($target, $uploadsRoot)) {
        return false;
    }

    return is_file($target) && unlink($target);
}
