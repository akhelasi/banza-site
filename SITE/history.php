<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$pageTitle = 'ისტორია - ' . $site['title'];
$currentPage = 'history';
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, $history['excerpt']);
?>

  <main id="main-content">
    <?php render_page_hero('ისტორია', $history['excerpt'], $site['hero_image'], 'ბანძის მეხსიერება'); ?>
    <section class="page-shell-narrow">
      <form class="filter-bar has-sort" data-live-filter data-filter-target="#historyContent" aria-label="ისტორიის გვერდის ძებნა">
        <label><span>ძებნა ამ გვერდზე</span><input type="search" name="search" placeholder="მაგ. ტაძარი, XVII საუკუნე, წყარო"></label>
        <label>
          <span>დალაგება</span>
          <select name="sort">
            <option value="title-asc">სათაური: ზრდადობით</option>
            <option value="title-desc">სათაური: კლებადობით</option>
          </select>
        </label>
      </form>
      <?php render_source_note($history); ?>
      <div class="content-stack" id="historyContent">
        <?php foreach ($history['body'] as $paragraph): ?>
          <article class="section-card filter-item" data-title="ისტორია" data-text="<?php echo e($paragraph); ?>" data-category="ისტორია" data-sort-title="ისტორია">
            <p><?php echo e($paragraph); ?></p>
          </article>
        <?php endforeach; ?>
        <article class="section-card filter-item" data-title="წყაროების შენიშვნა" data-text="<?php echo e($history['detail']); ?>" data-category="შენიშვნა" data-sort-title="წყაროების შენიშვნა">
          <h2>შემდეგი შესავსები მასალა</h2>
          <p><?php echo e($history['detail']); ?></p>
          <a class="inline-link" href="content-sources.md">წყაროების ნახვა →</a>
        </article>
      </div>
      <p class="empty-state" data-empty-state hidden>ამ გვერდზე შესაბამისი ტექსტი ვერ მოიძებნა.</p>
    </section>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);
