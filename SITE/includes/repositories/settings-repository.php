<?php

declare(strict_types=1);

function decode_mysql_setting(?string $value): array
{
    if ($value === null || trim($value) === '') {
        return [];
    }

    $decoded = json_decode($value, true);

    return is_array($decoded) ? $decoded : [];
}

function fetch_settings_from_mysql(PDO $pdo): array
{
    $statement = $pdo->query('SELECT setting_key, setting_value FROM settings');
    $settings = [];

    foreach ($statement->fetchAll() as $row) {
        $settings[(string) $row['setting_key']] = decode_mysql_setting($row['setting_value'] ?? null);
    }

    return $settings;
}

function fetch_social_links_from_mysql(PDO $pdo): array
{
    $statement = $pdo->query(
        'SELECT label, url, icon, post_date, last_update
         FROM social_links
         WHERE deleted_at IS NULL
         ORDER BY sort_order ASC, id ASC'
    );

    return array_map(static function (array $row): array {
        return [
            'label' => (string) ($row['label'] ?? ''),
            'href' => (string) ($row['url'] ?? ''),
            'icon' => (string) ($row['icon'] ?? ''),
            'post_date' => (string) ($row['post_date'] ?? ''),
            'last_update' => (string) ($row['last_update'] ?? ''),
        ];
    }, $statement->fetchAll());
}

function fetch_donation_accounts_from_mysql(PDO $pdo): array
{
    $statement = $pdo->query(
        'SELECT bank_name, account_number, account_holder, note, post_date, last_update
         FROM donation_accounts
         WHERE deleted_at IS NULL
         ORDER BY sort_order ASC, id ASC'
    );

    return array_map(static function (array $row): array {
        return [
            'bank' => (string) ($row['bank_name'] ?? ''),
            'account' => (string) ($row['account_number'] ?? ''),
            'holder' => (string) ($row['account_holder'] ?? ''),
            'note' => (string) ($row['note'] ?? ''),
            'post_date' => (string) ($row['post_date'] ?? ''),
            'last_update' => (string) ($row['last_update'] ?? ''),
        ];
    }, $statement->fetchAll());
}

function load_runtime_settings_from_mysql(PDO $pdo): array
{
    $settings = fetch_settings_from_mysql($pdo);

    return [
        'camera' => $settings['camera'] ?? [],
        'weather' => $settings['weather'] ?? [],
        'notifications' => $settings['notifications'] ?? [],
        'socialLinks' => fetch_social_links_from_mysql($pdo),
        'bankAccounts' => fetch_donation_accounts_from_mysql($pdo),
    ];
}
