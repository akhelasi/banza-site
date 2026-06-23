<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';

require_admin();
render_admin_header('Dashboard', 'dashboard');
?>

<section class="admin-grid">
  <article class="admin-stat"><span>სიახლეები</span><strong><?php echo count($news); ?></strong><a href="content.php?type=news">მართვა →</a></article>
  <article class="admin-stat"><span>პროექტები</span><strong><?php echo count($projects); ?></strong><a href="content.php?type=projects">მართვა →</a></article>
  <article class="admin-stat"><span>სტატიკური გვერდები</span><strong>5</strong><a href="content.php?type=pages">მართვა →</a></article>
  <article class="admin-stat"><span>მედია ფაილები</span><strong>3</strong><a href="media.php">ნახვა →</a></article>
</section>

<section class="admin-card">
  <h2>შემდეგი ფაზა</h2>
  <p>ეს admin skeleton მზადაა CRUD ფაზისთვის. შემდეგ დაემატება დამატება/რედაქტირება/soft delete, image upload და trash restore/permanent delete.</p>
</section>

<?php render_admin_footer(); ?>