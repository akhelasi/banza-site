<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$pageTitle = 'ჩვენ შესახებ - ' . $site['title'];
$currentPage = 'about';
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, $about['excerpt']);
?>

  <main id="main-content">
    <?php render_page_hero('ჩვენ შესახებ', $about['excerpt'], $site['hero_image'], 'სოფლის პორტრეტი'); ?>
    <section class="page-shell-narrow">
      <form class="filter-bar has-sort" data-live-filter data-filter-target="#aboutContent" aria-label="ჩვენ შესახებ გვერდის ძებნა">
        <label><span>ძებნა ამ გვერდზე</span><input type="search" name="search" placeholder="მაგ. მოსახლეობა, ვენახი, მდებარეობა"></label>
        <label>
          <span>დალაგება</span>
          <select name="sort">
            <option value="title-asc">სათაური: ზრდადობით</option>
            <option value="title-desc">სათაური: კლებადობით</option>
          </select>
        </label>
      </form>
      <div class="stats-grid page-stats">
        <?php foreach ($about['stats'] as $stat): ?>
          <div class="stat-item"><strong><?php echo e($stat['value']); ?></strong><span><?php echo e($stat['label']); ?></span><small><?php echo e($stat['note']); ?></small></div>
        <?php endforeach; ?>
      </div>
      <div class="content-stack" id="aboutContent">
        <?php foreach ($about['body'] as $paragraph): ?>
          <article class="section-card filter-item" data-title="ჩვენ შესახებ" data-text="<?php echo e($paragraph); ?>" data-category="ჩვენ შესახებ" data-sort-title="ჩვენ შესახებ"><p><?php echo e($paragraph); ?></p></article>
        <?php endforeach; ?>
      </div>
      <p class="empty-state" data-empty-state hidden>ამ გვერდზე შესაბამისი ტექსტი ვერ მოიძებნა.</p>
    </section>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);