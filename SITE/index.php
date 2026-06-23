<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';

$pageTitle = $site['title'] . ' - ' . $site['tagline'];
$currentPage = 'home';
render_header($site, $navigation, $socialLinks, $currentPage, $pageTitle);
?>

  <main id="main-content">
    <section class="hero-section" style="--hero-image: url('<?php echo e($site['hero_image']); ?>');" aria-labelledby="hero-title">
      <div class="hero-overlay"></div>
      <div class="hero-inner">
        <div class="hero-copy">
          <p class="eyebrow">მარტვილის მუნიციპალიტეტი</p>
          <h1 id="hero-title"><?php echo e($site['title']); ?><br><?php echo e($site['tagline']); ?></h1>
          <p><?php echo e($site['description']); ?></p>
          <div class="hero-buttons">
            <a class="button button-primary" href="about.php">ჩვენ შესახებ</a>
            <a class="button button-light" href="projects.php">პროექტები</a>
          </div>
        </div>

        <div class="hero-widgets" aria-label="ლაივ ინფორმაცია">
          <article class="glass-card camera-card">
            <div class="card-heading-row">
              <h2><?php echo e($camera['title']); ?></h2>
              <span class="live-pill"><?php echo e($camera['status']); ?></span>
            </div>
            <img src="<?php echo e($camera['preview_image']); ?>" alt="ბანძის ხედის დროებითი ფოტო">
            <button class="button button-primary full-width" type="button" data-modal-target="cameraModal">კამერის ნახვა</button>
          </article>

          <article class="glass-card weather-card" role="button" tabindex="0" data-modal-target="weatherModal" aria-label="ამინდის დეტალების ნახვა">
            <div class="card-heading-row">
              <h2>ბანძის ამინდი</h2>
              <span class="weather-icon" aria-hidden="true">☁</span>
            </div>
            <strong class="temperature"><?php echo e($weather['temperature']); ?></strong>
            <p><?php echo e($weather['summary']); ?></p>
            <dl class="weather-metrics">
              <div><dt>ქარი</dt><dd><?php echo e($weather['wind']); ?></dd></div>
              <div><dt>ტენიანობა</dt><dd><?php echo e($weather['humidity']); ?></dd></div>
              <div><dt>წვიმის ალბათობა</dt><dd><?php echo e($weather['rain']); ?></dd></div>
            </dl>
            <span class="inline-link">სრული პროგნოზი →</span>
          </article>
        </div>
      </div>
    </section>

    <section class="content-shell" aria-label="მთავარი კონტენტი">
      <div class="main-column">
        <a class="feature-card football-feature" href="football.php" aria-label="FC ოჯალეში ბანძას გვერდზე გადასვლა">
          <img src="<?php echo e($football['image']); ?>" alt="<?php echo e($football['title']); ?>">
          <div>
            <p class="eyebrow">სოფლის გუნდი</p>
            <h2><?php echo e($football['title']); ?></h2>
            <p><?php echo e($football['excerpt']); ?></p>
            <span class="inline-link">გუნდის გვერდი →</span>
          </div>
        </a>

        <section class="section-card" aria-labelledby="news-title">
          <div class="section-heading">
            <div>
              <p class="eyebrow">ბოლო ჩანაწერები</p>
              <h2 id="news-title">ახალი ამბები</h2>
            </div>
            <a class="inline-link" href="news.php">ყველა ამბავი →</a>
          </div>
          <div class="news-grid">
            <?php foreach (array_slice($news, 0, 3) as $item): ?>
              <article class="news-card">
                <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>">
                <div class="news-body">
                  <span class="date-badge"><?php echo e($item['date']); ?></span>
                  <p class="category"><?php echo e($item['category']); ?></p>
                  <h3><?php echo e($item['title']); ?></h3>
                  <p><?php echo e($item['excerpt']); ?></p>
                  <a class="inline-link" href="news-detail.php?slug=<?php echo e($item['slug']); ?>">წაკითხვა →</a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="section-card about-preview" aria-labelledby="about-title">
          <div class="section-heading compact">
            <div>
              <p class="eyebrow">სოფლის პორტრეტი</p>
              <h2 id="about-title"><?php echo e($about['title']); ?></h2>
            </div>
            <a class="inline-link" href="about.php">მეტის ნახვა →</a>
          </div>
          <p><?php echo e($about['excerpt']); ?></p>
          <div class="stats-grid">
            <?php foreach ($about['stats'] as $stat): ?>
              <div class="stat-item">
                <strong><?php echo e($stat['value']); ?></strong>
                <span><?php echo e($stat['label']); ?></span>
                <small><?php echo e($stat['note']); ?></small>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="section-card history-preview" aria-labelledby="history-title">
          <div class="history-image" aria-hidden="true"></div>
          <div>
            <p class="eyebrow">მეხსიერება</p>
            <h2 id="history-title"><?php echo e($history['title']); ?></h2>
            <p><?php echo e($history['excerpt']); ?></p>
            <p class="muted-note"><?php echo e($history['detail']); ?></p>
            <a class="inline-link" href="history.php">ისტორიის ნახვა →</a>
          </div>
        </section>
      </div>

      <aside class="sidebar" aria-label="დამატებითი ინფორმაცია">
        <section class="donation-card">
          <img src="<?php echo e(asset('images/donation-fund.png')); ?>" alt="ბანძას განვითარების ფონდი">
          <div>
            <h2>დონაცია</h2>
            <p>დაეხმარე სოფლის განვითარებას. ანგარიშები ამ ეტაპზე demo მონაცემებია.</p>
            <button class="button button-light full-width" type="button" data-modal-target="donationModal">დონაციის ანგარიშები</button>
          </div>
        </section>

        <section class="side-card" aria-labelledby="popular-projects-title">
          <div class="side-heading">
            <h2 id="popular-projects-title">პოპულარული პროექტები</h2>
            <a href="projects.php" aria-label="ყველა პროექტის ნახვა">→</a>
          </div>
          <div class="project-list">
            <?php foreach (array_slice($projects, 0, 3) as $project): ?>
              <a href="projects.php#<?php echo e($project['slug']); ?>">
                <span><?php echo e($project['status']); ?></span>
                <strong><?php echo e($project['title']); ?></strong>
                <small><?php echo e($project['excerpt']); ?></small>
              </a>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="side-card follow-card" aria-labelledby="follow-title">
          <h2 id="follow-title">გამოგვყევი</h2>
          <div class="follow-links">
            <?php foreach ($socialLinks as $link): ?>
              <a href="<?php echo e($link['href']); ?>" target="_blank" rel="noopener">
                <span><?php echo e($link['icon']); ?></span>
                <?php echo e($link['label']); ?>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      </aside>
    </section>
  </main>

  <div class="modal" id="cameraModal" aria-hidden="true" role="dialog" aria-labelledby="cameraModalTitle">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-panel large" role="document">
      <button class="modal-close" type="button" data-modal-close aria-label="ფანჯრის დახურვა">×</button>
      <h2 id="cameraModalTitle">ლაივ კამერა</h2>
      <div class="video-placeholder">
        <?php if (!empty($camera['stream_url'])): ?>
          <iframe src="<?php echo e($camera['stream_url']); ?>" title="ბანძის ლაივ კამერა" allowfullscreen loading="lazy"></iframe>
        <?php else: ?>
          <img src="<?php echo e($camera['preview_image']); ?>" alt="ბანძის კამერის დროებითი preview">
        <?php endif; ?>
        <p><?php echo e($camera['description']); ?></p>
      </div>
    </div>
  </div>

  <div class="modal" id="weatherModal" aria-hidden="true" role="dialog" aria-labelledby="weatherModalTitle">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-panel" role="document">
      <button class="modal-close" type="button" data-modal-close aria-label="ფანჯრის დახურვა">×</button>
      <h2 id="weatherModalTitle">ბანძის ამინდი</h2>
      <p class="modal-lead"><?php echo e($weather['source_label'] ?? 'Admin fallback'); ?><?php echo !empty($weather['updated_at']) ? ' · განახლდა: ' . e($weather['updated_at']) : ''; ?></p>
      <dl class="weather-detail-list">
        <div><dt>ტემპერატურა</dt><dd><?php echo e($weather['temperature']); ?></dd></div>
        <div><dt>ქარი</dt><dd><?php echo e($weather['wind']); ?></dd></div>
        <div><dt>ტენიანობა</dt><dd><?php echo e($weather['humidity']); ?></dd></div>
        <div><dt>წვიმის ალბათობა</dt><dd><?php echo e($weather['rain']); ?></dd></div>
      </dl>
      <h3>ახლომდებარე სოფლები და ქალაქები</h3>
      <div class="nearby-weather">
        <?php foreach ($weather['nearby'] as $place): ?>
          <div><strong><?php echo e($place['name']); ?></strong><span><?php echo e($place['forecast']); ?></span><b><?php echo e($place['temperature']); ?></b></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

<?php
render_donation_modal($bankAccounts);
render_footer($site);
