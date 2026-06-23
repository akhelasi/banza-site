<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$slug = isset($_GET['slug']) && preg_match('/^[a-z0-9-]+$/', (string) $_GET['slug']) ? (string) $_GET['slug'] : ($news[0]['slug'] ?? '');
$item = find_by_slug($news, $slug);

if ($item === null) {
    http_response_code(404);
    $item = [
        'title' => 'სიახლე ვერ მოიძებნა',
        'excerpt' => 'მითითებული ამბავი ჯერ არ არსებობს ან წაშლილია.',
        'image' => $site['hero_image'],
        'date' => '',
        'category' => '404',
        'body' => ['დაბრუნდით ახალი ამბების გვერდზე და აირჩიეთ სხვა ჩანაწერი.'],
        'gallery' => [],
        'videos' => [],
    ];
}

$pageTitle = $item['title'] . ' - ' . $site['title'];
$currentPage = 'news';
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, $item['excerpt']);
?>

  <main id="main-content">
    <?php render_page_hero($item['title'], $item['excerpt'], $item['image'], $item['category']); ?>

    <article class="article-shell">
      <a class="back-link" href="news.php">← ყველა ამბავი</a>
      <header class="article-header">
        <span class="date-badge"><?php echo e($item['date']); ?></span>
        <h1><?php echo e($item['title']); ?></h1>
      </header>
      <img class="article-main-image" src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>">
      <div class="article-body">
        <?php foreach ($item['body'] as $paragraph): ?>
          <p><?php echo e($paragraph); ?></p>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($item['gallery'])): ?>
        <section class="article-section" aria-labelledby="gallery-title">
          <h2 id="gallery-title">ფოტო გალერეა</h2>
          <div class="gallery-grid">
            <?php foreach ($item['gallery'] as $image): ?>
              <button type="button" data-lightbox-src="<?php echo e($image); ?>" data-lightbox-alt="<?php echo e($item['title']); ?>">
                <img src="<?php echo e($image); ?>" alt="<?php echo e($item['title']); ?>">
              </button>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if (!empty($item['videos'])): ?>
        <section class="article-section" aria-labelledby="video-title">
          <h2 id="video-title">ვიდეოები</h2>
          <div class="video-list">
            <?php foreach ($item['videos'] as $video): ?>
              <button class="video-card" type="button" data-video-url="<?php echo e($video['url']); ?>" data-video-title="<?php echo e($video['title']); ?>">
                <span aria-hidden="true">▶</span>
                <?php echo e($video['title']); ?>
              </button>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>
    </article>
  </main>

<?php
render_media_modal();
render_donation_modal($bankAccounts);
render_footer($site);