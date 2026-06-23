<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';

require_admin();
render_admin_header('პარამეტრები', 'settings');
?>

<section class="admin-card">
  <h2>საიტის პარამეტრები</h2>
  <p>შემდეგ ფაზაში აქ დაემატება social links, donation accounts, live camera URL, weather API/config და page hero settings.</p>
  <div class="settings-list">
    <article><strong>Social links</strong><span><?php echo count($socialLinks); ?> demo ბმული</span></article>
    <article><strong>Donation accounts</strong><span><?php echo count($bankAccounts); ?> demo ანგარიში</span></article>
    <article><strong>Live camera</strong><span><?php echo e($camera['status']); ?></span></article>
  </div>
</section>

<?php render_admin_footer(); ?>