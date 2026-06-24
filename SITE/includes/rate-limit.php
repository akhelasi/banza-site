<?php

declare(strict_types=1);

function rate_limit_storage_path(): string
{
    return dirname(__DIR__) . '/storage/rate-limit.json';
}

function rate_limit_client_identifier(): string
{
    $remoteAddress = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

    return $remoteAddress !== '' ? $remoteAddress : 'cli';
}

function rate_limit_key(string $action): string
{
    return hash('sha256', $action . '|' . rate_limit_client_identifier());
}

function rate_limit_read_store(): array
{
    $path = rate_limit_storage_path();
    if (!is_file($path)) {
        return [];
    }

    $json = file_get_contents($path);
    if ($json === false || trim($json) === '') {
        return [];
    }

    $decoded = json_decode($json, true);

    return is_array($decoded) ? $decoded : [];
}

function rate_limit_write_store(array $store): bool
{
    $path = rate_limit_storage_path();
    $directory = dirname($path);

    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        return false;
    }

    $json = json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents($path, $json . PHP_EOL, LOCK_EX) !== false;
}

function rate_limit_prune(array $store, int $now): array
{
    foreach ($store as $key => $bucket) {
        $resetAt = (int) ($bucket['reset_at'] ?? 0);
        if ($resetAt <= $now) {
            unset($store[$key]);
        }
    }

    return $store;
}

function rate_limit_hit(string $action, int $maxAttempts, int $windowSeconds): array
{
    $now = time();
    $key = rate_limit_key($action);
    $store = rate_limit_prune(rate_limit_read_store(), $now);
    $bucket = is_array($store[$key] ?? null) ? $store[$key] : [
        'attempts' => 0,
        'reset_at' => $now + $windowSeconds,
    ];

    $attempts = (int) ($bucket['attempts'] ?? 0);
    $resetAt = (int) ($bucket['reset_at'] ?? ($now + $windowSeconds));

    if ($attempts >= $maxAttempts) {
        $store[$key] = [
            'action' => $action,
            'attempts' => $attempts,
            'reset_at' => $resetAt,
        ];
        rate_limit_write_store($store);

        return [
            'allowed' => false,
            'remaining' => 0,
            'retry_after' => max(0, $resetAt - $now),
        ];
    }

    $attempts++;
    $store[$key] = [
        'action' => $action,
        'attempts' => $attempts,
        'reset_at' => $resetAt,
    ];
    rate_limit_write_store($store);

    return [
        'allowed' => true,
        'remaining' => max(0, $maxAttempts - $attempts),
        'retry_after' => max(0, $resetAt - $now),
    ];
}

function rate_limit_clear(string $action): void
{
    $store = rate_limit_read_store();
    $key = rate_limit_key($action);

    if (isset($store[$key])) {
        unset($store[$key]);
        rate_limit_write_store($store);
    }
}

function rate_limit_retry_minutes(int $retryAfterSeconds): int
{
    return max(1, (int) ceil($retryAfterSeconds / 60));
}
