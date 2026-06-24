<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$pageTitle = 'პროექტები - ' . $site['title'];
$currentPage = 'projects';
$statuses = array_values(array_unique(array_column($projects, 'status')));
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, 'ბანძის მიმდინარე და დაგეგმილი პროექტები.');
?>

  <main id="main-content">
    <?php render_page_hero('პროექტები', 'სოფლის საჭიროებები, იდეები და განვითარების ინიციატივები ერთ სივრცეში.', $site['hero_image'], 'განვითარება'); ?>
    <section class="page-shell-narrow">
      <form class="filter-bar has-sort" data-live-filter data-filter-target="#projectList" aria-label="პროექტების ძებნა, ფილტრი და დალაგება">
        <label><span>ძებნა</span><input type="search" name="search" placeholder="პროექტის სახელი ან აღწერა"></label>
        <label>
          <span>სტატუსი</span>
          <select name="category">
            <option value="">ყველა სტატუსი</option>
            <?php foreach ($statuses as $status): ?>
              <option value="<?php echo e($status); ?>"><?php echo e($status); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>
          <span>დალაგება</span>
          <select name="sort">
            <option value="title-asc">სათაური: ზრდადობით</option>
            <option value="title-desc">სათაური: კლებადობით</option>
            <option value="status-asc">სტატუსი: ზრდადობით</option>
            <option value="status-desc">სტატუსი: კლებადობით</option>
          </select>
        </label>
      </form>
      <div class="cards-grid project-cards" id="projectList">
        <?php foreach ($projects as $project): ?>
          <?php $projectText = plain_text($project['body'] ?? ''); ?>
          <article class="project-card filter-item" id="<?php echo e($project['slug']); ?>" data-title="<?php echo e($project['title']); ?>" data-text="<?php echo e(($project['excerpt'] ?? '') . ' ' . $projectText); ?>" data-category="<?php echo e($project['status']); ?>" data-sort-title="<?php echo e($project['title']); ?>" data-sort-status="<?php echo e($project['status']); ?>">
            <a class="project-card-link" href="project-detail.php?slug=<?php echo e($project['slug']); ?>">
              <img src="<?php echo e($project['image']); ?>" alt="<?php echo e($project['image_alt'] ?? $project['title']); ?>">
              <div>
                <span class="status-pill"><?php echo e($project['status']); ?></span>
                <p class="category"><?php echo e($project['category']); ?></p>
                <h2><?php echo e($project['title']); ?></h2>
                <p><?php echo e($project['excerpt'] ?? $projectText); ?></p>
                <span class="inline-link">სრულად ნახვა →</span>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="empty-state" data-empty-state hidden>ასეთი პროექტი ვერ მოიძებნა.</p>
    </section>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);
