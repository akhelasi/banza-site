<?php

declare(strict_types=1);

function content_import_json(mixed $value): string
{
    $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return is_string($json) ? $json : '';
}

function content_import_optional_date(array $item, string $key): ?string
{
    $value = trim((string) ($item[$key] ?? ''));
    return preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value) === 1 ? $value : null;
}

function content_import_datetime(?string $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
}

function content_import_deleted_at(array $item): ?string
{
    return content_import_datetime(isset($item['deleted_at']) ? (string) $item['deleted_at'] : null);
}

function content_import_source_key(string $prefix, string $value): string
{
    $slug = generate_slug($value, $prefix);
    return substr($prefix . ':' . $slug, 0, 190);
}

function content_import_static_pages(array $content): array
{
    $pages = [];
    foreach (['about', 'history', 'football', 'contact'] as $slug) {
        $page = $content[$slug] ?? null;
        if (!is_array($page)) {
            continue;
        }

        $title = trim((string) ($page['title'] ?? ucfirst($slug)));
        if ($title === '') {
            continue;
        }

        $pages[] = [
            'slug' => $slug,
            'title' => $title,
            'excerpt' => trim((string) ($page['excerpt'] ?? '')),
            'body' => content_import_json($page),
            'hero_image' => trim((string) ($page['hero_image'] ?? $page['image'] ?? '')),
            'status' => 'published',
            'source_status' => trim((string) ($page['source_status'] ?? 'demo')),
            'source_note' => trim((string) ($page['source_note'] ?? '')),
            'post_date' => content_import_optional_date($page, 'post_date'),
            'last_update' => content_import_optional_date($page, 'last_update'),
            'deleted_at' => content_import_deleted_at($page),
        ];
    }

    return $pages;
}

function content_import_posts(array $content): array
{
    $posts = [];

    foreach (['news' => 'news', 'projects' => 'project'] as $key => $type) {
        $items = is_array($content[$key] ?? null) ? $content[$key] : [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));
            $slug = trim((string) ($item['slug'] ?? ''));
            if ($title === '' || $slug === '') {
                continue;
            }

            $posts[] = [
                'type' => $type,
                'slug' => $slug,
                'title' => $title,
                'excerpt' => trim((string) ($item['excerpt'] ?? '')),
                'body' => plain_text($item['body'] ?? ''),
                'category' => trim((string) ($item['category'] ?? '')),
                'display_status' => $type === 'project' ? trim((string) ($item['status'] ?? '')) : '',
                'date_label' => $type === 'news' ? trim((string) ($item['date'] ?? '')) : '',
                'main_image' => trim((string) ($item['main_image'] ?? $item['image'] ?? '')),
                'published_at' => content_import_datetime((string) ($item['published_at'] ?? '')),
                'is_featured' => !empty($item['featured']) ? 1 : 0,
                'status' => empty($item['deleted_at']) ? 'published' : 'draft',
                'source_status' => trim((string) ($item['source_status'] ?? 'demo')),
                'source_note' => trim((string) ($item['source_note'] ?? '')),
                'post_date' => content_import_optional_date($item, 'post_date'),
                'last_update' => content_import_optional_date($item, 'last_update'),
                'deleted_at' => content_import_deleted_at($item),
                'gallery' => is_array($item['gallery'] ?? null) ? $item['gallery'] : [],
                'videos' => is_array($item['videos'] ?? null) ? $item['videos'] : [],
            ];
        }
    }

    return $posts;
}

function content_import_settings(array $content): array
{
    $settings = [];
    foreach (['camera', 'weather', 'notifications'] as $key) {
        if (is_array($content[$key] ?? null)) {
            $settings[] = [
                'setting_key' => $key,
                'setting_value' => content_import_json($content[$key]),
            ];
        }
    }

    return $settings;
}

function content_import_social_links(array $content): array
{
    $links = [];
    $items = is_array($content['socialLinks'] ?? null) ? $content['socialLinks'] : [];
    foreach ($items as $index => $item) {
        if (!is_array($item)) {
            continue;
        }

        $label = trim((string) ($item['label'] ?? ''));
        $url = trim((string) ($item['href'] ?? $item['url'] ?? ''));
        if ($label === '' || $url === '') {
            continue;
        }

        $links[] = [
            'source_key' => content_import_source_key('social', $label . '-' . $index),
            'label' => $label,
            'url' => $url,
            'icon' => trim((string) ($item['icon'] ?? '')),
            'sort_order' => $index,
            'post_date' => content_import_optional_date($item, 'post_date'),
            'last_update' => content_import_optional_date($item, 'last_update'),
            'deleted_at' => content_import_deleted_at($item),
        ];
    }

    return $links;
}

function content_import_donation_accounts(array $content): array
{
    $accounts = [];
    $items = is_array($content['bankAccounts'] ?? null) ? $content['bankAccounts'] : [];
    foreach ($items as $index => $item) {
        if (!is_array($item)) {
            continue;
        }

        $bank = trim((string) ($item['bank'] ?? $item['bank_name'] ?? ''));
        $account = trim((string) ($item['account'] ?? $item['account_number'] ?? ''));
        if ($bank === '' || $account === '') {
            continue;
        }

        $accounts[] = [
            'source_key' => content_import_source_key('donation', $bank . '-' . $account),
            'bank_name' => $bank,
            'account_number' => $account,
            'account_holder' => trim((string) ($item['account_holder'] ?? '')),
            'note' => trim((string) ($item['note'] ?? '')),
            'sort_order' => $index,
            'post_date' => content_import_optional_date($item, 'post_date'),
            'last_update' => content_import_optional_date($item, 'last_update'),
            'deleted_at' => content_import_deleted_at($item),
        ];
    }

    return $accounts;
}

function content_import_media_items(array $content): array
{
    $mediaItems = [];
    $items = is_array($content['mediaItems'] ?? null) ? $content['mediaItems'] : [];
    $index = 0;

    foreach ($items as $path => $item) {
        if (!is_array($item)) {
            continue;
        }

        $filePath = trim((string) ($item['path'] ?? $path));
        if ($filePath === '' || !str_starts_with($filePath, 'uploads/')) {
            continue;
        }

        $mediaItems[] = [
            'source_key' => content_import_source_key('media-item', $filePath),
            'post_id' => null,
            'file_path' => $filePath,
            'alt_text' => trim((string) ($item['alt'] ?? '')),
            'caption' => trim((string) ($item['caption'] ?? '')),
            'media_type' => 'image',
            'youtube_url' => null,
            'sort_order' => $index,
            'post_date' => content_import_optional_date($item, 'post_date'),
            'last_update' => content_import_optional_date($item, 'last_update'),
            'deleted_at' => content_import_deleted_at($item),
        ];
        $index++;
    }

    return $mediaItems;
}

function content_import_summary(array $content): array
{
    $posts = content_import_posts($content);
    $mediaCount = 0;
    foreach ($posts as $post) {
        $mediaCount += count($post['gallery']) + count($post['videos']);
    }

    $messages = is_array($content['contactMessages'] ?? null) ? $content['contactMessages'] : [];
    $importableMessages = array_values(array_filter($messages, static function (mixed $message): bool {
        if (!is_array($message)) {
            return false;
        }

        return trim((string) ($message['name'] ?? '')) !== ''
            && trim((string) ($message['email'] ?? '')) !== ''
            && trim((string) ($message['subject'] ?? '')) !== ''
            && trim((string) ($message['message'] ?? '')) !== '';
    }));

    return [
        'pages' => count(content_import_static_pages($content)),
        'posts' => count($posts),
        'media' => $mediaCount,
        'settings' => count(content_import_settings($content)),
        'social_links' => count(content_import_social_links($content)),
        'donation_accounts' => count(content_import_donation_accounts($content)),
        'media_items' => count(content_import_media_items($content)),
        'contact_messages' => count($importableMessages),
    ];
}

function import_pages_to_mysql(PDO $pdo, array $pages): int
{
    $statement = $pdo->prepare(
        'INSERT INTO pages (slug, title, excerpt, body, hero_image, status, source_status, source_note, post_date, last_update, deleted_at)
         VALUES (:slug, :title, :excerpt, :body, :hero_image, :status, :source_status, :source_note, :post_date, :last_update, :deleted_at)
         ON DUPLICATE KEY UPDATE
           title = VALUES(title),
           excerpt = VALUES(excerpt),
           body = VALUES(body),
           hero_image = VALUES(hero_image),
           status = VALUES(status),
           source_status = VALUES(source_status),
           source_note = VALUES(source_note),
           post_date = COALESCE(VALUES(post_date), post_date),
           last_update = COALESCE(VALUES(last_update), last_update),
           deleted_at = VALUES(deleted_at)'
    );

    $count = 0;
    foreach ($pages as $page) {
        $statement->execute($page);
        $count++;
    }

    return $count;
}

function import_posts_to_mysql(PDO $pdo, array $posts): array
{
    $postStatement = $pdo->prepare(
        'INSERT INTO posts (type, slug, title, excerpt, body, category, display_status, date_label, main_image, published_at, is_featured, status, source_status, source_note, post_date, last_update, deleted_at)
         VALUES (:type, :slug, :title, :excerpt, :body, :category, :display_status, :date_label, :main_image, :published_at, :is_featured, :status, :source_status, :source_note, :post_date, :last_update, :deleted_at)
         ON DUPLICATE KEY UPDATE
           type = VALUES(type),
           title = VALUES(title),
           excerpt = VALUES(excerpt),
           body = VALUES(body),
           category = VALUES(category),
           display_status = VALUES(display_status),
           date_label = VALUES(date_label),
           main_image = VALUES(main_image),
           published_at = VALUES(published_at),
           is_featured = VALUES(is_featured),
           status = VALUES(status),
           source_status = VALUES(source_status),
           source_note = VALUES(source_note),
           post_date = COALESCE(VALUES(post_date), post_date),
           last_update = COALESCE(VALUES(last_update), last_update),
           deleted_at = VALUES(deleted_at)'
    );
    $findPost = $pdo->prepare('SELECT id FROM posts WHERE slug = :slug LIMIT 1');
    $mediaStatement = $pdo->prepare(
        'INSERT INTO media (source_key, post_id, file_path, alt_text, caption, media_type, youtube_url, sort_order)
         VALUES (:source_key, :post_id, :file_path, :alt_text, :caption, :media_type, :youtube_url, :sort_order)
         ON DUPLICATE KEY UPDATE
           post_id = VALUES(post_id),
           file_path = VALUES(file_path),
           alt_text = VALUES(alt_text),
           caption = VALUES(caption),
           media_type = VALUES(media_type),
           youtube_url = VALUES(youtube_url),
           sort_order = VALUES(sort_order),
           deleted_at = NULL'
    );

    $postCount = 0;
    $mediaCount = 0;
    foreach ($posts as $post) {
        $postStatement->execute([
            'type' => $post['type'],
            'slug' => $post['slug'],
            'title' => $post['title'],
            'excerpt' => $post['excerpt'],
            'body' => $post['body'],
            'category' => $post['category'],
            'display_status' => $post['display_status'],
            'date_label' => $post['date_label'],
            'main_image' => $post['main_image'],
            'published_at' => $post['published_at'],
            'is_featured' => $post['is_featured'],
            'status' => $post['status'],
            'source_status' => $post['source_status'],
            'source_note' => $post['source_note'],
            'post_date' => $post['post_date'],
            'last_update' => $post['last_update'],
            'deleted_at' => $post['deleted_at'],
        ]);
        $postCount++;

        $findPost->execute(['slug' => $post['slug']]);
        $postId = (int) $findPost->fetchColumn();
        if ($postId <= 0) {
            continue;
        }

        foreach ($post['gallery'] as $index => $path) {
            $path = trim((string) $path);
            if ($path === '') {
                continue;
            }

            $mediaStatement->execute([
                'source_key' => content_import_source_key('media', $post['slug'] . '-gallery-' . $index),
                'post_id' => $postId,
                'file_path' => $path,
                'alt_text' => $post['title'],
                'caption' => '',
                'media_type' => 'image',
                'youtube_url' => null,
                'sort_order' => $index,
            ]);
            $mediaCount++;
        }

        foreach ($post['videos'] as $index => $video) {
            if (!is_array($video)) {
                continue;
            }

            $url = trim((string) ($video['url'] ?? ''));
            if ($url === '') {
                continue;
            }

            $mediaStatement->execute([
                'source_key' => content_import_source_key('media', $post['slug'] . '-video-' . $index),
                'post_id' => $postId,
                'file_path' => $url,
                'alt_text' => trim((string) ($video['title'] ?? $post['title'])),
                'caption' => '',
                'media_type' => 'youtube',
                'youtube_url' => $url,
                'sort_order' => 1000 + $index,
            ]);
            $mediaCount++;
        }
    }

    return ['posts' => $postCount, 'media' => $mediaCount];
}

function import_media_items_to_mysql(PDO $pdo, array $mediaItems): int
{
    $statement = $pdo->prepare(
        'INSERT INTO media (source_key, post_id, file_path, alt_text, caption, media_type, youtube_url, sort_order, post_date, last_update, deleted_at)
         VALUES (:source_key, :post_id, :file_path, :alt_text, :caption, :media_type, :youtube_url, :sort_order, :post_date, :last_update, :deleted_at)
         ON DUPLICATE KEY UPDATE
           post_id = VALUES(post_id),
           file_path = VALUES(file_path),
           alt_text = VALUES(alt_text),
           caption = VALUES(caption),
           media_type = VALUES(media_type),
           youtube_url = VALUES(youtube_url),
           sort_order = VALUES(sort_order),
           post_date = COALESCE(VALUES(post_date), post_date),
           last_update = COALESCE(VALUES(last_update), last_update),
           deleted_at = VALUES(deleted_at)'
    );

    $count = 0;
    foreach ($mediaItems as $item) {
        $statement->execute($item);
        $count++;
    }

    return $count;
}

function import_settings_to_mysql(PDO $pdo, array $settings): int
{
    $statement = $pdo->prepare(
        'INSERT INTO settings (setting_key, setting_value)
         VALUES (:setting_key, :setting_value)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );

    $count = 0;
    foreach ($settings as $setting) {
        $statement->execute($setting);
        $count++;
    }

    return $count;
}

function import_social_links_to_mysql(PDO $pdo, array $links): int
{
    $statement = $pdo->prepare(
        'INSERT INTO social_links (source_key, label, url, icon, sort_order, post_date, last_update, deleted_at)
         VALUES (:source_key, :label, :url, :icon, :sort_order, :post_date, :last_update, :deleted_at)
         ON DUPLICATE KEY UPDATE
           label = VALUES(label),
           url = VALUES(url),
           icon = VALUES(icon),
           sort_order = VALUES(sort_order),
           post_date = COALESCE(VALUES(post_date), post_date),
           last_update = COALESCE(VALUES(last_update), last_update),
           deleted_at = VALUES(deleted_at)'
    );

    $count = 0;
    foreach ($links as $link) {
        $statement->execute($link);
        $count++;
    }

    return $count;
}

function import_donation_accounts_to_mysql(PDO $pdo, array $accounts): int
{
    $statement = $pdo->prepare(
        'INSERT INTO donation_accounts (source_key, bank_name, account_number, account_holder, note, sort_order, post_date, last_update, deleted_at)
         VALUES (:source_key, :bank_name, :account_number, :account_holder, :note, :sort_order, :post_date, :last_update, :deleted_at)
         ON DUPLICATE KEY UPDATE
           bank_name = VALUES(bank_name),
           account_number = VALUES(account_number),
           account_holder = VALUES(account_holder),
           note = VALUES(note),
           sort_order = VALUES(sort_order),
           post_date = COALESCE(VALUES(post_date), post_date),
           last_update = COALESCE(VALUES(last_update), last_update),
           deleted_at = VALUES(deleted_at)'
    );

    $count = 0;
    foreach ($accounts as $account) {
        $statement->execute($account);
        $count++;
    }

    return $count;
}
