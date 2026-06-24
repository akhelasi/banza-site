<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$slug = isset($_GET['slug']) && preg_match('/^[a-z0-9-]+$/', (string) $_GET['slug']) ? (string) $_GET['slug'] : ($projects[0]['slug'] ?? '');
$project = find_by_slug($projects, $slug);

if ($project === null) {
    http_response_code(404);
    $project = [
        'title' => 'პროექტი ვერ მოიძებნა',
        'excerpt' => 'მითითებული პროექტი ჯერ არ არსებობს ან წაშლილია.',
        'image' => $site['hero_image'],
        'image_alt' => 'ბანძას სოფლის ხედი',
        'status' => '404',
        'category' => 'პროექტები',
        'body' => ['დაბრუნდით პროექტების გვერდზე და აირჩიეთ სხვა პროექტი.'],
    ];
}

$projectBody = content_paragraphs($project['body'] ?? '');
$pageTitle = $project['title'] . ' - ' . $site['title'];
$currentPage = 'projects';
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, $project['excerpt'] ?? '');
?>

  <main id="main-content">
    <?php render_page_hero($project['title'], $project['excerpt'] ?? '', $project['image'], $project['status'] ?? 'პროექტი'); ?>

    <article class="article-shell project-detail-page">
      <a class="back-link" href="projects.php">← ყველა პროექტი</a>
      <header class="article-header">
        <span class="status-pill"><?php echo e($project['status'] ?? ''); ?></span>
        <p class="category"><?php echo e($project['category'] ?? ''); ?></p>
        <h1><?php echo e($project['title']); ?></h1>
      </header>
      <img class="article-main-image" src="<?php echo e($project['image']); ?>" alt="<?php echo e($project['image_alt'] ?? $project['title']); ?>">
      <div class="article-body">
        <?php foreach ($projectBody as $paragraph): ?>
          <p><?php echo e($paragraph); ?></p>
        <?php endforeach; ?>
      </div>
    </article>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);
