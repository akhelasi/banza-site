<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require __DIR__ . '/../includes/uploads.php';

require_admin();

$type = (string) ($_GET['type'] ?? ($_POST['type'] ?? 'news'));
$action = (string) ($_GET['action'] ?? 'list');
$allowedTypes = ['news', 'projects', 'pages'];

if (!in_array($type, $allowedTypes, true)) {
    http_response_code(404);
    $type = 'news';
    admin_flash('მითითებული სექცია ვერ მოიძებნა.', 'error');
}

$content = $contentStore ?? [];
$pageKeys = ['about', 'history', 'football', 'contact'];

function admin_items_key(string $type): string
{
    return $type === 'projects' ? 'projects' : 'news';
}

function admin_section_title(string $type): string
{
    return match ($type) {
        'projects' => 'პროექტები',
        'pages' => 'გვერდები',
        default => 'ახალი ამბები',
    };
}

function admin_section_nav_key(string $type): string
{
    return match ($type) {
        'projects' => 'projects',
        'pages' => 'pages',
        default => 'news',
    };
}

function item_by_slug(array $items, string $slug): ?array
{
    foreach ($items as $item) {
        if (($item['slug'] ?? '') === $slug) {
            return $item;
        }
    }

    return null;
}

function item_index_by_slug(array $items, string $slug): ?int
{
    foreach ($items as $index => $item) {
        if (($item['slug'] ?? '') === $slug) {
            return $index;
        }
    }

    return null;
}

function slug_exists(array $items, string $slug, string $originalSlug = ''): bool
{
    foreach ($items as $item) {
        if (($item['slug'] ?? '') === $slug && $slug !== $originalSlug) {
            return true;
        }
    }

    return false;
}

function parse_videos(string $value): array
{
    $videos = [];
    foreach (split_lines($value) as $line) {
        $parts = array_map('trim', explode('|', $line, 2));
        if (count($parts) === 1) {
            $videos[] = ['title' => 'ვიდეო', 'url' => $parts[0]];
            continue;
        }
        $videos[] = ['title' => $parts[0] !== '' ? $parts[0] : 'ვიდეო', 'url' => $parts[1]];
    }

    return $videos;
}

function parse_stats(string $value): array
{
    $stats = [];
    foreach (split_lines($value) as $line) {
        $parts = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
        if ($parts[0] === '' || $parts[1] === '') {
            continue;
        }
        $stats[] = ['value' => $parts[0], 'label' => $parts[1], 'note' => $parts[2]];
    }

    return $stats;
}

function parse_contact_items(string $value): array
{
    $items = [];
    foreach (split_lines($value) as $line) {
        $parts = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
        if ($parts[0] === '' || $parts[1] === '') {
            continue;
        }
        $items[] = ['label' => $parts[0], 'value' => $parts[1], 'note' => $parts[2]];
    }

    return $items;
}

function textarea_body(array|string|null $body): string
{
    return is_array($body) ? implode(PHP_EOL . PHP_EOL, $body) : (string) $body;
}

function gallery_text(array $gallery): string
{
    return implode(PHP_EOL, array_map('strval', $gallery));
}

function videos_text(array $videos): string
{
    return implode(PHP_EOL, array_map(static function (array $video): string {
        return trim(($video['title'] ?? 'ვიდეო') . ' | ' . ($video['url'] ?? ''));
    }, $videos));
}

function stats_text(array $stats): string
{
    return implode(PHP_EOL, array_map(static function (array $stat): string {
        return trim(($stat['value'] ?? '') . ' | ' . ($stat['label'] ?? '') . ' | ' . ($stat['note'] ?? ''));
    }, $stats));
}

function contact_items_text(array $items): string
{
    return implode(PHP_EOL, array_map(static function (array $item): string {
        return trim(($item['label'] ?? '') . ' | ' . ($item['value'] ?? '') . ' | ' . ($item['note'] ?? ''));
    }, $items));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        admin_flash('უსაფრთხოების token არასწორია.', 'error');
        redirect('content.php?type=' . urlencode($type));
    }

    $postAction = (string) ($_POST['action'] ?? '');

    if ($postAction === 'delete' && in_array($type, ['news', 'projects'], true)) {
        $key = admin_items_key($type);
        $slug = (string) ($_POST['slug'] ?? '');
        $index = item_index_by_slug($content[$key] ?? [], $slug);

        if ($index === null) {
            admin_flash('ჩანაწერი ვერ მოიძებნა.', 'error');
            redirect('content.php?type=' . urlencode($type));
        }

        $content[$key][$index]['deleted_at'] = date(DATE_ATOM);
        $content[$key][$index] = touch_content_dates($content[$key][$index]);
        save_content_store($content);
        admin_flash('ჩანაწერი გადავიდა სანაგვეში.');
        redirect('content.php?type=' . urlencode($type));
    }

    if ($postAction === 'save' && in_array($type, ['news', 'projects'], true)) {
        $key = admin_items_key($type);
        $originalSlug = (string) ($_POST['original_slug'] ?? '');
        $title = trim((string) ($_POST['title'] ?? ''));
        $slug = strtolower(trim((string) ($_POST['slug'] ?? '')));
        $slug = $slug !== '' ? $slug : generate_slug($title, $type === 'news' ? 'news' : 'project');

        if ($title === '' || !validate_slug($slug)) {
            admin_flash('სათაური და სწორი slug აუცილებელია. გამოიყენეთ მხოლოდ latin ასოები, ციფრები და ტირე.', 'error');
            redirect('content.php?type=' . urlencode($type) . ($originalSlug !== '' ? '&action=edit&slug=' . urlencode($originalSlug) : '&action=create'));
        }

        $items = $content[$key] ?? [];
        if (slug_exists($items, $slug, $originalSlug)) {
            admin_flash('ამ slug-ით ჩანაწერი უკვე არსებობს.', 'error');
            redirect('content.php?type=' . urlencode($type) . ($originalSlug !== '' ? '&action=edit&slug=' . urlencode($originalSlug) : '&action=create'));
        }

        $existing = $originalSlug !== '' ? (item_by_slug($items, $originalSlug) ?? []) : [];
        $imagePath = trim((string) ($_POST['image'] ?? ($existing['image'] ?? '')));
        $uploadError = null;
        $uploadedImage = isset($_FILES['main_image']) ? store_uploaded_image($_FILES['main_image'], $uploadError) : null;
        if ($uploadError !== null) {
            admin_flash($uploadError, 'error');
            redirect('content.php?type=' . urlencode($type) . ($originalSlug !== '' ? '&action=edit&slug=' . urlencode($originalSlug) : '&action=create'));
        }
        if ($uploadedImage !== null) {
            $imagePath = $uploadedImage;
        }

        $body = split_lines((string) ($_POST['body'] ?? ''));
        $item = [
            'slug' => $slug,
            'title' => $title,
            'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
            'body' => $body !== [] ? $body : [''],
            'image' => $imagePath,
            'category' => trim((string) ($_POST['category'] ?? '')),
            'deleted_at' => '',
        ];

        if ($type === 'news') {
            $gallery = split_lines((string) ($_POST['gallery'] ?? ''));
            if (isset($_FILES['gallery_images'])) {
                foreach (normalize_files_array($_FILES['gallery_images']) as $galleryFile) {
                    $galleryError = null;
                    $galleryPath = store_uploaded_image($galleryFile, $galleryError);
                    if ($galleryError !== null) {
                        admin_flash($galleryError, 'error');
                        redirect('content.php?type=' . urlencode($type) . ($originalSlug !== '' ? '&action=edit&slug=' . urlencode($originalSlug) : '&action=create'));
                    }
                    if ($galleryPath !== null) {
                        $gallery[] = $galleryPath;
                    }
                }
            }

            $item['date'] = trim((string) ($_POST['date'] ?? ''));
            $item['published_at'] = trim((string) ($_POST['published_at'] ?? date('Y-m-d')));
            $item['gallery'] = array_values(array_unique($gallery));
            $item['videos'] = parse_videos((string) ($_POST['videos'] ?? ''));
        } else {
            $item['status'] = trim((string) ($_POST['status'] ?? 'იდეა'));
            $item['featured'] = isset($_POST['featured']);
        }

        $index = $originalSlug !== '' ? item_index_by_slug($items, $originalSlug) : null;
        if ($index === null) {
            $item = touch_content_dates($item, true);
            $items[] = $item;
        } else {
            $items[$index] = touch_content_dates(array_replace($items[$index], $item));
        }

        $content[$key] = $items;
        save_content_store($content);
        admin_flash('ჩანაწერი შენახულია.');
        redirect('content.php?type=' . urlencode($type));
    }

    if ($postAction === 'save_page' && $type === 'pages') {
        $pageKey = (string) ($_POST['page_key'] ?? '');
        if (!in_array($pageKey, $pageKeys, true)) {
            admin_flash('გვერდი ვერ მოიძებნა.', 'error');
            redirect('content.php?type=pages');
        }

        $page = $content[$pageKey] ?? [];
        $page['title'] = trim((string) ($_POST['title'] ?? ($page['title'] ?? '')));
        $page['excerpt'] = trim((string) ($_POST['excerpt'] ?? ($page['excerpt'] ?? '')));
        $body = split_lines((string) ($_POST['body'] ?? ''));
        if ($body !== []) {
            $page['body'] = $body;
        }
        if (isset($_POST['image'])) {
            $page['image'] = trim((string) $_POST['image']);
        }
        if ($pageKey === 'about') {
            $stats = parse_stats((string) ($_POST['stats'] ?? ''));
            if ($stats !== []) {
                $page['stats'] = $stats;
            }
        }
        if ($pageKey === 'contact') {
            $contactItems = parse_contact_items((string) ($_POST['items'] ?? ''));
            if ($contactItems !== []) {
                $page['items'] = $contactItems;
            }
        }

        $content[$pageKey] = touch_content_dates($page, empty($page['post_date']));
        save_content_store($content);
        admin_flash('გვერდი შენახულია.');
        redirect('content.php?type=pages');
    }
}

$title = admin_section_title($type);
render_admin_header($title, admin_section_nav_key($type));

if ($action === 'create' && in_array($type, ['news', 'projects'], true)) {
    $item = [
        'slug' => '', 'title' => '', 'excerpt' => '', 'body' => [], 'image' => '', 'category' => '',
        'date' => date('d F'), 'published_at' => date('Y-m-d'), 'gallery' => [], 'videos' => [],
        'status' => 'იდეა', 'featured' => false,
    ];
    $action = 'form';
}

if ($action === 'edit' && in_array($type, ['news', 'projects'], true)) {
    $slug = (string) ($_GET['slug'] ?? '');
    $item = item_by_slug($content[admin_items_key($type)] ?? [], $slug);
    if ($item === null || !empty($item['deleted_at'])) {
        admin_flash('ჩანაწერი ვერ მოიძებნა.', 'error');
        redirect('content.php?type=' . urlencode($type));
    }
    $action = 'form';
}

if ($action === 'edit' && $type === 'pages') {
    $pageKey = (string) ($_GET['page'] ?? '');
    if (!in_array($pageKey, $pageKeys, true)) {
        admin_flash('გვერდი ვერ მოიძებნა.', 'error');
        redirect('content.php?type=pages');
    }
    $page = $content[$pageKey] ?? [];
    ?>
    <section class="admin-card">
      <div class="admin-card-heading"><h2><?php echo e($page['title'] ?? $pageKey); ?></h2><a class="inline-link" href="content.php?type=pages">← უკან</a></div>
      <form class="admin-form" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="save_page">
        <input type="hidden" name="type" value="pages">
        <input type="hidden" name="page_key" value="<?php echo e($pageKey); ?>">
        <label><span>სათაური</span><input type="text" name="title" value="<?php echo e($page['title'] ?? ''); ?>" required></label>
        <label><span>მოკლე აღწერა</span><textarea name="excerpt" required><?php echo e($page['excerpt'] ?? ''); ?></textarea></label>
        <label><span>ტექსტი</span><textarea name="body" rows="9"><?php echo e(textarea_body($page['body'] ?? [])); ?></textarea></label>
        <?php if ($pageKey === 'football'): ?>
          <label><span>სურათი</span><input type="text" name="image" value="<?php echo e($page['image'] ?? ''); ?>"></label>
        <?php endif; ?>
        <?php if ($pageKey === 'about'): ?>
          <label><span>სტატისტიკა: value | label | note</span><textarea name="stats" rows="5"><?php echo e(stats_text($page['stats'] ?? [])); ?></textarea></label>
        <?php endif; ?>
        <?php if ($pageKey === 'contact'): ?>
          <label><span>კონტაქტები: label | value | note</span><textarea name="items" rows="5"><?php echo e(contact_items_text($page['items'] ?? [])); ?></textarea></label>
        <?php endif; ?>
        <button class="button button-primary" type="submit">შენახვა</button>
      </form>
    </section>
    <?php
    render_admin_footer();
    exit;
}

if ($action === 'form') {
    $isNews = $type === 'news';
    ?>
    <section class="admin-card">
      <div class="admin-card-heading"><h2><?php echo e(($item['title'] ?? '') !== '' ? 'რედაქტირება' : 'დამატება'); ?></h2><a class="inline-link" href="content.php?type=<?php echo e($type); ?>">← უკან</a></div>
      <form class="admin-form" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="type" value="<?php echo e($type); ?>">
        <input type="hidden" name="original_slug" value="<?php echo e($item['slug'] ?? ''); ?>">
        <div class="admin-form-grid">
          <label><span>სათაური</span><input type="text" name="title" value="<?php echo e($item['title'] ?? ''); ?>" required></label>
          <label><span>Slug latin ასოებით</span><input type="text" name="slug" value="<?php echo e($item['slug'] ?? ''); ?>" placeholder="my-news-slug"></label>
        </div>
        <label><span>მოკლე აღწერა</span><textarea name="excerpt" required><?php echo e($item['excerpt'] ?? ''); ?></textarea></label>
        <label><span>სრული ტექსტი, აბზაცები ცარიელი ხაზით გამოყავით</span><textarea name="body" rows="9"><?php echo e(textarea_body($item['body'] ?? [])); ?></textarea></label>
        <div class="admin-form-grid">
          <label><span>სურათი / URL</span><input type="text" name="image" value="<?php echo e($item['image'] ?? ''); ?>"></label>
          <label><span>ან ატვირთე მთავარი სურათი</span><input type="file" name="main_image" accept="image/jpeg,image/png,image/webp,image/gif"></label>
          <label><span>კატეგორია</span><input type="text" name="category" value="<?php echo e($item['category'] ?? ''); ?>"></label>
        </div>
        <?php if ($isNews): ?>
          <div class="admin-form-grid">
            <label><span>თარიღი ტექსტად</span><input type="text" name="date" value="<?php echo e($item['date'] ?? ''); ?>"></label>
            <label><span>Published date</span><input type="date" name="published_at" value="<?php echo e($item['published_at'] ?? date('Y-m-d')); ?>"></label>
          </div>
          <label><span>გალერეა, თითო URL ახალ ხაზზე</span><textarea name="gallery" rows="4"><?php echo e(gallery_text($item['gallery'] ?? [])); ?></textarea></label>
          <label><span>ან ატვირთე გალერეის სურათები</span><input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple></label>
          <label><span>ვიდეოები: title | youtube_url</span><textarea name="videos" rows="4"><?php echo e(videos_text($item['videos'] ?? [])); ?></textarea></label>
        <?php else: ?>
          <div class="admin-form-grid">
            <label><span>სტატუსი</span><input type="text" name="status" value="<?php echo e($item['status'] ?? 'იდეა'); ?>"></label>
            <label class="checkbox-label"><input type="checkbox" name="featured" <?php echo !empty($item['featured']) ? 'checked' : ''; ?>> გამორჩეული პროექტი</label>
          </div>
        <?php endif; ?>
        <button class="button button-primary" type="submit">შენახვა</button>
      </form>
    </section>
    <?php
    render_admin_footer();
    exit;
}

if ($type === 'pages') {
    $pages = [
        'about' => $content['about'] ?? [],
        'history' => $content['history'] ?? [],
        'football' => $content['football'] ?? [],
        'contact' => $content['contact'] ?? [],
    ];
    ?>
    <section class="admin-card">
      <div class="admin-card-heading"><div><p class="eyebrow">Static content</p><h2>გვერდები</h2></div></div>
      <form class="filter-bar single admin-filter" data-live-filter data-filter-target="#adminPagesList" aria-label="გვერდების ძებნა">
        <label><span>ძებნა</span><input type="search" name="search" placeholder="სათაური ან აღწერა"></label>
        <label>
          <span>დალაგება</span>
          <select name="sort">
            <option value="title-asc">სათაური: ზრდადობით</option>
            <option value="title-desc">სათაური: კლებადობით</option>
          </select>
        </label>
      </form>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>გვერდი</th><th>აღწერა</th><th>ქმედება</th></tr></thead>
          <tbody id="adminPagesList">
            <?php foreach ($pages as $pageKey => $page): ?>
              <tr class="filter-item" data-title="<?php echo e($page['title'] ?? $pageKey); ?>" data-text="<?php echo e(($page['excerpt'] ?? '') . ' ' . $pageKey); ?>" data-sort-title="<?php echo e($page['title'] ?? $pageKey); ?>"><td><?php echo e($page['title'] ?? $pageKey); ?></td><td><?php echo e($page['excerpt'] ?? ''); ?></td><td><a class="inline-link" href="content.php?type=pages&action=edit&page=<?php echo e($pageKey); ?>">რედაქტირება →</a></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="empty-state" data-empty-state hidden>ასეთი გვერდი ვერ მოიძებნა.</p>
    </section>
    <?php
    render_admin_footer();
    exit;
}

$key = admin_items_key($type);
$items = visible_content_items($content[$key] ?? []);
$itemCategories = array_values(array_unique(array_filter(array_map(static function (array $item) use ($type): string {
    return trim((string) ($type === 'projects' ? ($item['status'] ?? '') : ($item['category'] ?? '')));
}, $items))));
sort($itemCategories, SORT_NATURAL | SORT_FLAG_CASE);
?>

<section class="admin-card">
  <div class="admin-card-heading">
    <div>
      <p class="eyebrow">Content CRUD</p>
      <h2><?php echo e($title); ?></h2>
    </div>
    <a class="button button-primary" href="content.php?type=<?php echo e($type); ?>&action=create">დამატება</a>
  </div>
  <form class="filter-bar has-sort admin-filter" data-live-filter data-filter-target="#adminContentList" aria-label="კონტენტის ძებნა, ფილტრი და დალაგება">
    <label><span>ძებნა</span><input type="search" name="search" placeholder="სათაური, slug ან აღწერა"></label>
    <label>
      <span><?php echo $type === 'projects' ? 'სტატუსი' : 'კატეგორია'; ?></span>
      <select name="category">
        <option value="">ყველა</option>
        <?php foreach ($itemCategories as $category): ?>
          <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>
      <span>დალაგება</span>
      <select name="sort">
        <option value="date-desc">თარიღი: ახალი ჯერ</option>
        <option value="date-asc">თარიღი: ძველი ჯერ</option>
        <option value="title-asc">სათაური: ზრდადობით</option>
        <option value="title-desc">სათაური: კლებადობით</option>
      </select>
    </label>
  </form>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>სათაური</th><th>აღწერა</th><th>სტატუსი/კატეგორია</th><th>ქმედება</th></tr></thead>
      <tbody id="adminContentList">
        <?php foreach ($items as $item): ?>
          <?php
          $itemCategory = $type === 'projects' ? ($item['status'] ?? '') : ($item['category'] ?? '');
          $sortDate = $item['published_at'] ?? $item['post_date'] ?? $item['created_at'] ?? '';
          ?>
          <tr class="filter-item" data-title="<?php echo e($item['title'] ?? ''); ?>" data-text="<?php echo e(($item['excerpt'] ?? '') . ' ' . ($item['slug'] ?? '')); ?>" data-category="<?php echo e($itemCategory); ?>" data-sort-title="<?php echo e($item['title'] ?? ''); ?>" data-sort-date="<?php echo e($sortDate); ?>">
            <td><?php echo e($item['title'] ?? ''); ?><small><?php echo e($item['slug'] ?? ''); ?></small></td>
            <td><?php echo e($item['excerpt'] ?? ''); ?></td>
            <td><?php echo e($itemCategory); ?></td>
            <td class="admin-actions">
              <a class="inline-link" href="content.php?type=<?php echo e($type); ?>&action=edit&slug=<?php echo e($item['slug'] ?? ''); ?>">რედაქტირება</a>
              <form method="post" onsubmit="return confirm('ჩანაწერი გადავიდეს სანაგვეში?');">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="type" value="<?php echo e($type); ?>">
                <input type="hidden" name="slug" value="<?php echo e($item['slug'] ?? ''); ?>">
                <button type="submit">წაშლა</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="empty-state" data-empty-state hidden>ასეთი ჩანაწერი ვერ მოიძებნა.</p>
</section>

<?php render_admin_footer(); ?>
