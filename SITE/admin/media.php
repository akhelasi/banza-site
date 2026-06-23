<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require __DIR__ . '/../includes/uploads.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        admin_flash('უსაფრთხოების token არასწორია.', 'error');
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
    <label><span>სურათი JPG, PNG, WEBP ან GIF, მაქს. 5MB</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required></label>
    <button class="button button-primary" type="submit">ატვირთვა</button>
  </form>
</section>

<section class="admin-card">
  <div class="admin-card-heading"><h2>ატვირთული ფაილები</h2><span><?php echo count($uploadedItems); ?> ფაილი</span></div>
  <?php if ($uploadedItems === []): ?>
    <p class="muted-note">ჯერ ატვირთული ფაილები არ არის. ატვირთვის შემდეგ path გამოიყენე სიახლეში ან პროექტში.</p>
  <?php else: ?>
    <div class="admin-media-grid">
      <?php foreach ($uploadedItems as $item): ?>
        <article>
          <img src="<?php echo e('../' . $item['path']); ?>" alt="<?php echo e($item['name']); ?>">
          <strong><?php echo e($item['name']); ?></strong>
          <code><?php echo e($item['path']); ?></code>
          <span><?php echo e(number_format($item['size'] / 1024, 1)); ?> KB · <?php echo e($item['modified']); ?></span>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="admin-card">
  <div class="admin-card-heading"><h2>Seed assets</h2></div>
  <div class="admin-media-grid">
    <?php foreach ($seedMediaItems as $item): ?>
      <article><img src="<?php echo e($item['path']); ?>" alt="<?php echo e($item['title']); ?>"><strong><?php echo e($item['title']); ?></strong><code><?php echo e(str_replace('../', '', $item['path'])); ?></code><span><?php echo e($item['type']); ?></span></article>
    <?php endforeach; ?>
  </div>
</section>

<?php render_admin_footer(); ?>