<?php

declare(strict_types=1);

function mysql_post_date_label(array $row): string
{
    $dateLabel = trim((string) ($row['date_label'] ?? ''));
    if ($dateLabel !== '') {
        return $dateLabel;
    }

    $publishedAt = trim((string) ($row['published_at'] ?? ''));
    if ($publishedAt === '') {
        return '';
    }

    $timestamp = strtotime($publishedAt);
    return $timestamp === false ? '' : date('d/m/Y', $timestamp);
}

function mysql_post_published_date(array $row): string
{
    $publishedAt = trim((string) ($row['published_at'] ?? ''));
    if ($publishedAt === '') {
        return '';
    }

    $timestamp = strtotime($publishedAt);
    return $timestamp === false ? '' : date('Y-m-d', $timestamp);
}

function mysql_post_media(PDO $pdo, int $postId): array
{
    $statement = $pdo->prepare(
        "SELECT file_path, alt_text, media_type, youtube_url
         FROM media
         WHERE post_id = :post_id AND deleted_at IS NULL
         ORDER BY sort_order ASC, id ASC"
    );
    $statement->execute(['post_id' => $postId]);

    $gallery = [];
    $videos = [];
    foreach ($statement->fetchAll() as $row) {
        $type = (string) ($row['media_type'] ?? 'image');
        if ($type === 'youtube') {
            $url = trim((string) ($row['youtube_url'] ?? $row['file_path'] ?? ''));
            if ($url !== '') {
                $videos[] = [
                    'title' => (string) ($row['alt_text'] ?? ''),
                    'url' => $url,
                ];
            }
            continue;
        }

        $path = trim((string) ($row['file_path'] ?? ''));
        if ($path !== '') {
            $gallery[] = $path;
        }
    }

    return ['gallery' => $gallery, 'videos' => $videos];
}

function mysql_post_row_to_runtime(PDO $pdo, array $row): array
{
    $postId = (int) ($row['id'] ?? 0);
    $media = $postId > 0 ? mysql_post_media($pdo, $postId) : ['gallery' => [], 'videos' => []];
    $type = (string) ($row['type'] ?? 'news');
    $image = trim((string) ($row['main_image'] ?? ''));
    $category = trim((string) ($row['category'] ?? ''));
    $displayStatus = trim((string) ($row['display_status'] ?? ''));

    $item = [
        'slug' => (string) ($row['slug'] ?? ''),
        'title' => (string) ($row['title'] ?? ''),
        'excerpt' => (string) ($row['excerpt'] ?? ''),
        'source_status' => (string) ($row['source_status'] ?? 'demo'),
        'source_note' => (string) ($row['source_note'] ?? ''),
        'body' => content_paragraphs($row['body'] ?? ''),
        'image' => $image,
        'main_image' => $image,
        'published_at' => mysql_post_published_date($row),
        'post_date' => (string) ($row['post_date'] ?? ''),
        'last_update' => (string) ($row['last_update'] ?? ''),
        'gallery' => $media['gallery'],
        'videos' => $media['videos'],
    ];

    if ($type === 'project') {
        $item['status'] = $displayStatus !== '' ? $displayStatus : 'პროექტი';
        $item['category'] = $category;
        $item['featured'] = !empty($row['is_featured']);
    } else {
        $item['date'] = mysql_post_date_label($row);
        $item['category'] = $category;
    }

    return $item;
}

function fetch_posts_from_mysql(PDO $pdo, string $type): array
{
    $statement = $pdo->prepare(
        "SELECT id, type, slug, title, excerpt, body, category, display_status, date_label, main_image, published_at, is_featured, source_status, source_note, post_date, last_update
         FROM posts
         WHERE type = :type AND status = 'published' AND deleted_at IS NULL
         ORDER BY COALESCE(published_at, created_at) DESC, id DESC"
    );
    $statement->execute(['type' => $type]);

    $items = [];
    foreach ($statement->fetchAll() as $row) {
        $items[] = mysql_post_row_to_runtime($pdo, $row);
    }

    return $items;
}

function load_runtime_posts_from_mysql(PDO $pdo): array
{
    return [
        'news' => fetch_posts_from_mysql($pdo, 'news'),
        'projects' => fetch_posts_from_mysql($pdo, 'project'),
    ];
}
