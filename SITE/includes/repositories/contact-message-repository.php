<?php

declare(strict_types=1);

function normalize_contact_message_for_mysql(array $message): array
{
    $slug = trim((string) ($message['slug'] ?? ''));
    if ($slug === '') {
        $slug = generate_slug('message-' . ($message['name'] ?? 'contact'), 'message');
    }

    return [
        'slug' => substr($slug, 0, 160),
        'name' => substr(trim((string) ($message['name'] ?? '')), 0, 120),
        'email' => substr(trim((string) ($message['email'] ?? '')), 0, 190),
        'phone' => substr(trim((string) ($message['phone'] ?? '')), 0, 60),
        'subject' => substr(trim((string) ($message['subject'] ?? '')), 0, 160),
        'message' => trim((string) ($message['message'] ?? '')),
        'read_at' => normalize_mysql_datetime($message['read_at'] ?? null),
        'post_date' => normalize_display_date($message['post_date'] ?? null),
        'last_update' => normalize_display_date($message['last_update'] ?? null),
        'created_at' => normalize_mysql_datetime($message['created_at'] ?? null),
        'deleted_at' => normalize_mysql_datetime($message['deleted_at'] ?? null),
    ];
}

function normalize_mysql_datetime(mixed $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return null;
    }

    return date('Y-m-d H:i:s', $timestamp);
}

function normalize_display_date(mixed $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value) === 1) {
        return $value;
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return null;
    }

    return date('d/m/Y', $timestamp);
}

function upsert_contact_message(PDO $pdo, array $message): void
{
    $row = normalize_contact_message_for_mysql($message);

    $sql = <<<'SQL'
INSERT INTO contact_messages (
  slug, name, email, phone, subject, message, read_at, post_date, last_update, created_at, deleted_at
) VALUES (
  :slug, :name, :email, :phone, :subject, :message, :read_at, :post_date, :last_update, COALESCE(:created_at, CURRENT_TIMESTAMP), :deleted_at
)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  email = VALUES(email),
  phone = VALUES(phone),
  subject = VALUES(subject),
  message = VALUES(message),
  read_at = VALUES(read_at),
  post_date = COALESCE(VALUES(post_date), post_date),
  last_update = COALESCE(VALUES(last_update), DATE_FORMAT(CURRENT_DATE(), '%d/%m/%Y')),
  deleted_at = VALUES(deleted_at)
SQL;

    $statement = $pdo->prepare($sql);
    $statement->execute($row);
}

function import_contact_messages_to_mysql(PDO $pdo, array $messages): int
{
    $count = 0;

    foreach ($messages as $message) {
        if (!is_array($message)) {
            continue;
        }

        $row = normalize_contact_message_for_mysql($message);
        if ($row['name'] === '' || $row['email'] === '' || $row['subject'] === '' || $row['message'] === '') {
            continue;
        }

        upsert_contact_message($pdo, $row);
        $count += 1;
    }

    return $count;
}
