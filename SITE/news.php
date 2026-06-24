<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$pageTitle = 'ახალი ამბები - ' . $site['title'];
$currentPage = 'news';
$categories = array_values(array_unique(array_column($news, 'category')));
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, 'ბანძის ბოლო ამბები, განცხადებები და სოფლის სიახლეები.');
?>

  <main id="main-content">
    <?php render_page_hero('ახალი ამბები', 'ბანძის სიახლეები, განცხადებები, სპორტი, ფონდის ინფორმაცია და სოფლის მიმდინარე ამბები.', $site['hero_image'], 'სოფლის პულსი'); ?>

    <section class="page-shell-narrow">
      <form class="filter-bar has-sort" data-live-filter data-filter-target="#newsList" data-page-size="6" data-load-more-target="#newsLoadMore" aria-label="ახალი ამბების ძებნა, ფილტრი და დალაგება">
        <label>
          <span>ძებნა</span>
          <input type="search" name="search" placeholder="სათაური, აღწერა ან კატეგორია">
        </label>
        <label>
          <span>კატეგორია</span>
          <select name="category">
            <option value="">ყველა კატეგორია</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>
          <span>დალაგება</span>
          <select name="sort">
            <option value="date-desc">თარიღი: კლებადობით</option>
            <option value="date-asc">თარიღი: ზრდადობით</option>
            <option value="title-asc">სათაური: ზრდადობით</option>
            <option value="title-desc">სათაური: კლებადობით</option>
          </select>
        </label>
        <p class="filter-note">ფილტრი და დალაგება მუშაობს live რეჟიმში და გვერდს არ ტვირთავს თავიდან.</p>
      </form>

      <div class="cards-grid listing-grid" id="newsList">
        <?php foreach ($news as $item): ?>
          <article class="news-card filter-item" data-title="<?php echo e($item['title']); ?>" data-text="<?php echo e($item['excerpt']); ?>" data-category="<?php echo e($item['category']); ?>" data-sort-title="<?php echo e($item['title']); ?>" data-sort-date="<?php echo e($item['published_at']); ?>">
            <a href="news-detail.php?slug=<?php echo e($item['slug']); ?>">
              <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['image_alt'] ?? $item['title']); ?>">
              <div class="news-body">
                <span class="date-badge"><?php echo e($item['date']); ?></span>
                <p class="category"><?php echo e($item['category']); ?></p>
                <h2><?php echo e($item['title']); ?></h2>
                <p><?php echo e($item['excerpt']); ?></p>
                <span class="inline-link">სრულად ნახვა →</span>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="empty-state" data-empty-state hidden>ამ ძებნით ჩანაწერი ვერ მოიძებნა.</p>
      <button class="button button-primary load-more-button" id="newsLoadMore" type="button" hidden>მეტის ჩვენება</button>
    </section>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);
