<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';

require_admin();
render_admin_header('ფაილები', 'media');
$mediaItems = [
    ['title' => 'Football team', 'path' => asset('images/football-team.png'), 'type' => 'image'],
    ['title' => 'Donation fund', 'path' => asset('images/donation-fund.png'), 'type' => 'image'],
    ['title' => 'Banza logo', 'path' => asset('images/banza-logo.svg'), 'type' => 'logo'],
];
?>

<section class="admin-card">
  <div class="admin-card-heading"><h2>მედია ბიბლიოთეკა</h2><button class="button button-primary" type="button" disabled>ატვირთვა შემდეგ ფაზაში</button></div>
  <div class="admin-media-grid">
    <?php foreach ($mediaItems as $item): ?>
      <article><img src="<?php echo e($item['path']); ?>" alt="<?php echo e($item['title']); ?>"><strong><?php echo e($item['title']); ?></strong><span><?php echo e($item['type']); ?></span></article>
    <?php endforeach; ?>
  </div>
</section>

<?php render_admin_footer(); ?>