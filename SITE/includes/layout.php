<?php

declare(strict_types=1);

function render_header(array $site, array $navigation, array $socialLinks, string $currentPage, string $pageTitle, string $description = ''): void
{
    $metaDescription = $description !== '' ? $description : ($site['description'] ?? '');
    ?>
<!doctype html>
<html lang="ka">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?php echo e($metaDescription); ?>">
  <title><?php echo e($pageTitle); ?></title>
  <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body>
  <a class="skip-link" href="#main-content">კონტენტზე გადასვლა</a>

  <header class="site-header" aria-label="საიტის მთავარი ნავიგაცია">
    <div class="header-inner">
      <a class="brand" href="<?php echo e(page_url()); ?>" aria-label="ბანძა - მთავარი გვერდი">
        <img class="brand-logo" src="<?php echo e(asset('images/banza-logo.svg')); ?>" alt="ბანძა - ჩვენი სოფელი">
      </a>

      <nav class="main-nav" aria-label="ძირითადი მენიუ">
        <?php foreach ($navigation as $item): ?>
          <a href="<?php echo e($item['href']); ?>"<?php echo is_active_nav($currentPage, $item['key']); ?>><?php echo e($item['label']); ?></a>
        <?php endforeach; ?>
      </nav>

      <div class="header-actions" aria-label="სოციალური ბმულები">
        <?php foreach ($socialLinks as $link): ?>
          <a class="social-link" href="<?php echo e($link['href']); ?>" target="_blank" rel="noopener" aria-label="<?php echo e($link['label']); ?>">
            <?php echo e($link['icon']); ?>
          </a>
        <?php endforeach; ?>
        <button class="support-button" type="button" data-modal-target="donationModal">მე ❤ ბანძა</button>
      </div>
    </div>
  </header>
<?php
}

function render_page_hero(string $title, string $description, string $image, string $eyebrow = ''): void
{
    ?>
    <section class="page-hero" style="--page-hero-image: url('<?php echo e($image); ?>');" aria-labelledby="page-title">
      <div class="page-hero-inner">
        <?php if ($eyebrow !== ''): ?>
          <p class="eyebrow"><?php echo e($eyebrow); ?></p>
        <?php endif; ?>
        <h1 id="page-title"><?php echo e($title); ?></h1>
        <p><?php echo e($description); ?></p>
      </div>
    </section>
<?php
}

function render_footer(array $site): void
{
    ?>
  <footer class="site-footer">
    <p>© <?php echo date('Y'); ?> <?php echo e($site['title']); ?>. Demo content მზადდება admin panel-ის შემდეგი ფაზისთვის.</p>
  </footer>

  <script src="<?php echo e(asset('js/main.js')); ?>"></script>
</body>
</html>
<?php
}

function render_donation_modal(array $bankAccounts): void
{
    ?>
  <div class="modal" id="donationModal" aria-hidden="true" role="dialog" aria-labelledby="donationModalTitle">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-panel" role="document">
      <button class="modal-close" type="button" data-modal-close aria-label="ფანჯრის დახურვა">×</button>
      <h2 id="donationModalTitle">დონაციის ანგარიშები</h2>
      <p class="modal-lead">ეს demo ანგარიშებია. რეალური საბანკო მონაცემები admin panel-იდან დაემატება.</p>
      <div class="bank-list">
        <?php foreach ($bankAccounts as $account): ?>
          <article>
            <h3><?php echo e($account['bank']); ?></h3>
            <p><?php echo e($account['account']); ?></p>
            <small><?php echo e($account['note']); ?></small>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php
}

function render_media_modal(): void
{
    ?>
  <div class="modal" id="mediaModal" aria-hidden="true" role="dialog" aria-labelledby="mediaModalTitle">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-panel media-panel" role="document">
      <button class="modal-close" type="button" data-modal-close aria-label="ფანჯრის დახურვა">×</button>
      <h2 id="mediaModalTitle">მედია</h2>
      <div class="media-modal-body" data-media-modal-body></div>
    </div>
  </div>
<?php
}