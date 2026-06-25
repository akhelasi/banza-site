<?php

declare(strict_types=1);

function decode_mysql_page_body(?string $value): array
{
    if ($value === null || trim($value) === '') {
        return [];
    }

    $decoded = json_decode($value, true);
    if (is_array($decoded)) {
        return $decoded;
    }

    return ['body' => content_paragraphs($value)];
}

function page_row_to_runtime(array $row): array
{
    $page = decode_mysql_page_body($row['body'] ?? null);
    $heroImage = trim((string) ($row['hero_image'] ?? ''));

    $page['title'] = (string) ($row['title'] ?? ($page['title'] ?? ''));
    $page['excerpt'] = (string) ($row['excerpt'] ?? ($page['excerpt'] ?? ''));
    $page['source_status'] = (string) ($row['source_status'] ?? ($page['source_status'] ?? 'demo'));
    $page['source_note'] = (string) ($row['source_note'] ?? ($page['source_note'] ?? ''));
    $page['post_date'] = (string) ($row['post_date'] ?? ($page['post_date'] ?? ''));
    $page['last_update'] = (string) ($row['last_update'] ?? ($page['last_update'] ?? ''));

    if ($heroImage !== '') {
        $page['hero_image'] = $heroImage;
        $page['image'] = $page['image'] ?? $heroImage;
    }

    return $page;
}

function fetch_pages_from_mysql(PDO $pdo): array
{
    $statement = $pdo->query(
        "SELECT slug, title, excerpt, body, hero_image, source_status, source_note, post_date, last_update
         FROM pages
         WHERE status = 'published' AND deleted_at IS NULL"
    );

    $pages = [];
    foreach ($statement->fetchAll() as $row) {
        $slug = (string) ($row['slug'] ?? '');
        if ($slug === '') {
            continue;
        }

        $pages[$slug] = page_row_to_runtime($row);
    }

    return $pages;
}

function fetch_media_items_from_mysql(PDO $pdo): array
{
    $statement = $pdo->query(
        "SELECT file_path, alt_text, caption, media_type, post_date, last_update
         FROM media
         WHERE post_id IS NULL AND deleted_at IS NULL
         ORDER BY sort_order ASC, id ASC"
    );

    $items = [];
    foreach ($statement->fetchAll() as $row) {
        $path = trim((string) ($row['file_path'] ?? ''));
        if ($path === '') {
            continue;
        }

        $items[$path] = [
            'path' => $path,
            'alt' => (string) ($row['alt_text'] ?? ''),
            'caption' => (string) ($row['caption'] ?? ''),
            'type' => (string) ($row['media_type'] ?? 'image'),
            'post_date' => (string) ($row['post_date'] ?? ''),
            'last_update' => (string) ($row['last_update'] ?? ''),
        ];
    }

    return $items;
}

function load_runtime_pages_from_mysql(PDO $pdo): array
{
    $pages = fetch_pages_from_mysql($pdo);

    return [
        'about' => $pages['about'] ?? [],
        'history' => $pages['history'] ?? [],
        'football' => $pages['football'] ?? [],
        'contact' => $pages['contact'] ?? [],
        'mediaItems' => fetch_media_items_from_mysql($pdo),
    ];
}
