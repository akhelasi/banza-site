<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';

require_admin();
$activeMessages = visible_content_items($contactMessages ?? []);
$unreadMessages = array_filter($activeMessages, static fn (array $message): bool => empty($message['read_at']));
render_admin_header('Dashboard', 'dashboard');
?>

<section class="admin-grid">
  <article class="admin-stat"><span>სიახლეები</span><strong><?php echo count($news); ?></strong><a href="content.php?type=news">მართვა →</a></article>
  <article class="admin-stat"><span>პროექტები</span><strong><?php echo count($projects); ?></strong><a href="content.php?type=projects">მართვა →</a></article>
  <article class="admin-stat"><span>შეტყობინებები</span><strong><?php echo count($unreadMessages); ?></strong><a href="messages.php">ნახვა →</a></article>
  <article class="admin-stat"><span>მედია ფაილები</span><strong>3</strong><a href="media.php">ნახვა →</a></article>
</section>

<section class="admin-card">
  <h2>მართვის პანელი</h2>
  <p>აქედან იმართება სიახლეები, პროექტები, სტატიკური გვერდები, ფაილები, პარამეტრები, საკონტაქტო შეტყობინებები და სანაგვე.</p>
</section>

<?php render_admin_footer(); ?>
