<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$pageTitle = 'კონტაქტი - ' . $site['title'];
$currentPage = 'contact';
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle, $contact['excerpt']);
?>

  <main id="main-content">
    <?php render_page_hero('კონტაქტი', $contact['excerpt'], $site['hero_image'], 'კავშირი'); ?>
    <section class="page-shell-narrow">
      <form class="filter-bar has-sort" data-live-filter data-filter-target="#contactList" aria-label="კონტაქტების ძებნა და დალაგება">
        <label><span>ძებნა კონტაქტებში</span><input type="search" name="search" placeholder="ელფოსტა, ტელეფონი ან ლოკაცია"></label>
        <label>
          <span>დალაგება</span>
          <select name="sort">
            <option value="title-asc">სახელი: ზრდადობით</option>
            <option value="title-desc">სახელი: კლებადობით</option>
          </select>
        </label>
      </form>
      <div class="cards-grid contact-grid" id="contactList">
        <?php foreach ($contact['items'] as $item): ?>
          <article class="contact-card filter-item" data-title="<?php echo e($item['label']); ?>" data-text="<?php echo e($item['value'] . ' ' . $item['note']); ?>" data-category="კონტაქტი" data-sort-title="<?php echo e($item['label']); ?>">
            <span><?php echo e($item['label']); ?></span>
            <strong><?php echo e($item['value']); ?></strong>
            <p><?php echo e($item['note']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
      <section class="section-card contact-note">
        <h2>შეტყობინების ფორმა</h2>
        <p>ფორმა admin/backend ფაზაში ჩაირთვება. მანამდე საკონტაქტო მონაცემები demo რეჟიმშია.</p>
        <form class="contact-form" aria-label="Demo საკონტაქტო ფორმა">
          <label><span>სახელი</span><input type="text" disabled placeholder="შემდეგ ფაზაში ჩაირთვება"></label>
          <label><span>შეტყობინება</span><textarea disabled placeholder="შემდეგ ფაზაში ჩაირთვება"></textarea></label>
          <button class="button button-primary" type="button" disabled>გაგზავნა</button>
        </form>
      </section>
      <p class="empty-state" data-empty-state hidden>ასეთი საკონტაქტო ინფორმაცია ვერ მოიძებნა.</p>
    </section>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);