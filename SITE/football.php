<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$pageTitle = $football['title'] . ' - ' . $site['title'];
$currentPage = 'football';
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, $football['excerpt']);
?>

  <main id="main-content">
    <?php render_page_hero($football['title'], $football['excerpt'], $football['image'], 'სოფლის გუნდი'); ?>
    <section class="article-shell football-page">
      <img class="article-main-image" src="<?php echo e($football['image']); ?>" alt="<?php echo e($football['image_alt'] ?? $football['title']); ?>">
      <div class="article-body">
        <?php foreach ($football['body'] as $paragraph): ?>
          <p><?php echo e($paragraph); ?></p>
        <?php endforeach; ?>
      </div>
      <section class="article-section">
        <h2>შემდეგ ეტაპზე დაემატება</h2>
        <div class="cards-grid mini-cards">
          <article><strong>გუნდის ისტორია</strong><p>ტექსტები admin panel-იდან განახლდება.</p></article>
          <article><strong>ფოტო გალერეა</strong><p>კომპიუტერიდან ატვირთული სურათები შეინახება საიტის ფოლდერში.</p></article>
          <article><strong>ვიდეოები</strong><p>YouTube ბმულები modal player-ით გაიხსნება.</p></article>
        </div>
      </section>
    </section>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);