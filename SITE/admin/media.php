<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require __DIR__ . '/../includes/uploads.php';

require_admin();

$content = $contentStore ?? [];

function media_meta_for(array $mediaItems, string $path): array
{
    $meta = $mediaItems[$path] ?? [];
    return is_array($meta) ? $meta : [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        admin_flash('უსაფრთხოების token არასწორია.', 'error');
        redirect('media.php');
    }

    $postAction = (string) ($_POST['action'] ?? 'upload');

    if ($postAction === 'save_meta') {
        $path = trim((string) ($_POST['path'] ?? ''));
        if (!str_starts_with($path, 'uploads/')) {
            admin_flash('მედია ფაილი ვერ მოიძებნა.', 'error');
            redirect('media.php');
        }

        $knownUploadPaths = array_column(list_uploaded_images(), 'path');
        if (!in_array($path, $knownUploadPaths, true)) {
            admin_flash('მედია ფაილი ვერ მოიძებნა.', 'error');
            redirect('media.php');
        }

        $content['mediaItems'] = $content['mediaItems'] ?? [];
        $content['mediaItems'][$path] = [
            'path' => $path,
            'alt' => trim((string) ($_POST['alt'] ?? '')),
            'caption' => trim((string) ($_POST['caption'] ?? '')),
            'last_update' => content_date_today(),
        ];

        if (admin_save_content_store($content)) {
            admin_flash('მედია აღწერა შენახულია.');
        }
        redirect('media.php');
    }

    $error = null;
    $stored = isset($_FILES['image']) ? store_uploaded_image($_FILES['image'], $error) : null;
    if ($error !== null) {
        admin_flash($error, 'error');
        redirect('media.php');
    }

    if ($stored !== null) {
        admin_flash('სურათი აიტვირთა: ' . $stored);
        redirect('media.php');
    }

    admin_flash('ასატვირთი ფაილი არ არის არჩეული.', 'error');
    redirect('media.php');
}

render_admin_header('ფაილები', 'media');
$seedMediaItems = [
    ['title' => 'Football team', 'path' => asset('images/football-team.png'), 'type' => 'seed image'],
    ['title' => 'Donation fund', 'path' => asset('images/donation-fund.png'), 'type' => 'seed image'],
    ['title' => 'Banza logo', 'path' => asset('images/banza-logo.svg'), 'type' => 'logo'],
];
$uploadedItems = list_uploaded_images();
?>

<section class="admin-card">
  <div class="admin-card-heading"><h2>სურათის ატვირთვა</h2></div>
  <form class="admin-form" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="action" value="upload">
    <label><span>სურათი JPG, PNG, WEBP ან GIF, მაქს. 5MB. დიდი JPG/PNG/WEBP ფაილები ავტომატურად შემცირდება, თუ server-ზე GD ჩართულია.</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required></label>
    <button class="button button-primary" type="submit">ატვირთვა</button>
  </form>
</section>

<section class="admin-card">
  <div class="admin-card-heading"><h2>ატვირთული ფაილები</h2><span><?php echo count($uploadedItems); ?> ფაილი</span></div>
  <?php if ($uploadedItems === []): ?>
    <p class="muted-note">ჯერ ატვირთული ფაილები არ არის. ატვირთვის შემდეგ path გამოიყენე სიახლეში ან პროექტში.</p>
  <?php else: ?>
    <form class="filter-bar has-sort admin-filter" data-live-filter data-filter-target="#uploadedMediaList" aria-label="ატვირთული ფაილების ძებნა, ფილტრი და დალაგება">
      <label><span>ძებნა</span><input type="search" name="search" placeholder="ფაილის სახელი ან path"></label>
      <label>
        <span>ტიპი</span>
        <select name="category">
          <option value="">ყველა</option>
          <option value="jpg">JPG</option>
          <option value="jpeg">JPEG</option>
          <option value="png">PNG</option>
          <option value="webp">WEBP</option>
          <option value="gif">GIF</option>
        </select>
      </label>
      <label>
        <span>დალაგება</span>
        <select name="sort">
          <option value="date-desc">თარიღი: ახალი ჯერ</option>
          <option value="date-asc">თარიღი: ძველი ჯერ</option>
          <option value="title-asc">სახელი: ზრდადობით</option>
          <option value="title-desc">სახელი: კლებადობით</option>
          <option value="size-desc">ზომა: დიდი ჯერ</option>
          <option value="size-asc">ზომა: პატარა ჯერ</option>
        </select>
      </label>
    </form>
    <div class="admin-media-grid" id="uploadedMediaList">
      <?php foreach ($uploadedItems as $item): ?>
        <?php $extension = strtolower(pathinfo((string) $item['name'], PATHINFO_EXTENSION)); ?>
        <?php $meta = media_meta_for($mediaItems ?? [], (string) $item['path']); ?>
        <article class="filter-item" data-title="<?php echo e($item['name']); ?>" data-text="<?php echo e($item['path']); ?>" data-category="<?php echo e($extension); ?>" data-sort-title="<?php echo e($item['name']); ?>" data-sort-date="<?php echo e($item['modified']); ?>" data-sort-size="<?php echo e((int) $item['size']); ?>">
          <img src="<?php echo e('../' . $item['path']); ?>" alt="<?php echo e($meta['alt'] ?? $item['name']); ?>">
          <strong><?php echo e($item['name']); ?></strong>
          <code><?php echo e($item['path']); ?></code>
          <span><?php echo e(number_format($item['size'] / 1024, 1)); ?> KB · <?php echo e($item['modified']); ?></span>
          <?php if (!empty($meta['caption'])): ?>
            <p class="muted-note"><?php echo e($meta['caption']); ?></p>
          <?php endif; ?>
          <form class="media-meta-form" method="post">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="save_meta">
            <input type="hidden" name="path" value="<?php echo e($item['path']); ?>">
            <label><span>Alt text</span><input type="text" name="alt" value="<?php echo e($meta['alt'] ?? ''); ?>" placeholder="მოკლე აღწერა"></label>
            <label><span>Caption</span><textarea name="caption" rows="2" placeholder="სურათის წარწერა"><?php echo e($meta['caption'] ?? ''); ?></textarea></label>
            <button type="submit">შენახვა</button>
          </form>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="empty-state" data-empty-state hidden>ასეთი ფაილი ვერ მოიძებნა.</p>
  <?php endif; ?>
</section>

<section class="admin-card">
  <div class="admin-card-heading"><h2>Seed assets</h2></div>
  <form class="filter-bar has-sort admin-filter" data-live-filter data-filter-target="#seedMediaList" aria-label="seed assets ძებნა, ფილტრი და დალაგება">
    <label><span>ძებნა</span><input type="search" name="search" placeholder="სახელი ან path"></label>
    <label>
      <span>ტიპი</span>
      <select name="category">
        <option value="">ყველა</option>
        <option value="seed image">Seed image</option>
        <option value="logo">Logo</option>
      </select>
    </label>
    <label>
      <span>დალაგება</span>
      <select name="sort">
        <option value="title-asc">სახელი: ზრდადობით</option>
        <option value="title-desc">სახელი: კლებადობით</option>
      </select>
    </label>
  </form>
  <div class="admin-media-grid" id="seedMediaList">
    <?php foreach ($seedMediaItems as $item): ?>
      <?php $seedPath = str_replace('../', '', $item['path']); ?>
      <article class="filter-item" data-title="<?php echo e($item['title']); ?>" data-text="<?php echo e($seedPath); ?>" data-category="<?php echo e($item['type']); ?>" data-sort-title="<?php echo e($item['title']); ?>"><img src="<?php echo e($item['path']); ?>" alt="<?php echo e($item['title']); ?>"><strong><?php echo e($item['title']); ?></strong><code><?php echo e($seedPath); ?></code><span><?php echo e($item['type']); ?></span></article>
    <?php endforeach; ?>
  </div>
  <p class="empty-state" data-empty-state hidden>ასეთი seed asset ვერ მოიძებნა.</p>
</section>

<?php render_admin_footer(); ?>
