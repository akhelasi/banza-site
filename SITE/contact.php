<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/database.php';
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/repositories/contact-message-repository.php';

$contactErrors = [];
$contactFlash = null;
$oldContact = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'subject' => '',
    'message' => '',
];

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['contact_flash'])) {
    $contactFlash = (string) $_SESSION['contact_flash'];
    unset($_SESSION['contact_flash']);
}

function contact_post_value(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldContact = [
        'name' => contact_post_value('name'),
        'email' => contact_post_value('email'),
        'phone' => contact_post_value('phone'),
        'subject' => contact_post_value('subject'),
        'message' => contact_post_value('message'),
    ];

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $contactErrors[] = 'უსაფრთხოების token არასწორია. განაახლეთ გვერდი და სცადეთ თავიდან.';
    }

    if (contact_post_value('website') !== '') {
        $contactErrors[] = 'შეტყობინება ვერ გაიგზავნა.';
    }

    if (mb_strlen($oldContact['name']) < 2 || mb_strlen($oldContact['name']) > 120) {
        $contactErrors[] = 'სახელი უნდა იყოს 2-120 სიმბოლოს შორის.';
    }

    if (!filter_var($oldContact['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($oldContact['email']) > 190) {
        $contactErrors[] = 'ელფოსტა არასწორია.';
    }

    if ($oldContact['phone'] !== '' && mb_strlen($oldContact['phone']) > 60) {
        $contactErrors[] = 'ტელეფონის ველი ძალიან გრძელია.';
    }

    if (mb_strlen($oldContact['subject']) < 3 || mb_strlen($oldContact['subject']) > 160) {
        $contactErrors[] = 'სათაური უნდა იყოს 3-160 სიმბოლოს შორის.';
    }

    if (mb_strlen($oldContact['message']) < 10 || mb_strlen($oldContact['message']) > 3000) {
        $contactErrors[] = 'შეტყობინება უნდა იყოს 10-3000 სიმბოლოს შორის.';
    }

    if ($contactErrors === []) {
        $message = touch_content_dates([
            'slug' => generate_slug('message-' . $oldContact['name'] . '-' . bin2hex(random_bytes(4)), 'message'),
            'name' => $oldContact['name'],
            'email' => $oldContact['email'],
            'phone' => $oldContact['phone'],
            'subject' => $oldContact['subject'],
            'message' => $oldContact['message'],
            'created_at' => date('c'),
            'read_at' => '',
            'deleted_at' => '',
        ], true);

        $saved = false;
        if (content_storage_driver() === 'mysql') {
            create_contact_message_in_mysql(db(), $message);
            $saved = true;
        } else {
            $content = $contentStore ?? [];
            $content['contactMessages'] = is_array($content['contactMessages'] ?? null) ? $content['contactMessages'] : [];
            $content['contactMessages'][] = $message;
            $saved = save_content_store($content);
        }

        if ($saved) {
            $_SESSION['contact_flash'] = 'შეტყობინება გაიგზავნა. ადმინისტრატორი ნახავს მას მართვის პანელში.';
            redirect('contact.php#contactForm');
        }

        $contactErrors[] = 'შეტყობინება ვერ შეინახა. სცადეთ მოგვიანებით.';
    }
}

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
      <section class="section-card contact-note" id="contactForm">
        <h2>შეტყობინების ფორმა</h2>
        <p>გამოიყენეთ ფორმა სოფლის ამბების, პროექტების, ფოტოების ან ფონდის საკითხებზე დასაკავშირებლად.</p>
        <?php if ($contactFlash): ?>
          <div class="flash"><?php echo e($contactFlash); ?></div>
        <?php endif; ?>
        <?php if ($contactErrors !== []): ?>
          <div class="flash flash-error">
            <?php foreach ($contactErrors as $error): ?>
              <p><?php echo e($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form class="contact-form" method="post" aria-label="საკონტაქტო ფორმა">
          <?php echo csrf_field(); ?>
          <label class="honeypot-field"><span>Website</span><input type="text" name="website" tabindex="-1" autocomplete="off"></label>
          <div class="contact-form-grid">
            <label><span>სახელი</span><input type="text" name="name" value="<?php echo e($oldContact['name']); ?>" required maxlength="120" autocomplete="name"></label>
            <label><span>ელფოსტა</span><input type="email" name="email" value="<?php echo e($oldContact['email']); ?>" required maxlength="190" autocomplete="email"></label>
          </div>
          <div class="contact-form-grid">
            <label><span>ტელეფონი</span><input type="tel" name="phone" value="<?php echo e($oldContact['phone']); ?>" maxlength="60" autocomplete="tel"></label>
            <label><span>სათაური</span><input type="text" name="subject" value="<?php echo e($oldContact['subject']); ?>" required maxlength="160"></label>
          </div>
          <label><span>შეტყობინება</span><textarea name="message" required maxlength="3000"><?php echo e($oldContact['message']); ?></textarea></label>
          <p class="form-hint">შეტყობინება ინახება admin panel-ში. production-ზე ელფოსტით გაგზავნა ცალკე SMTP კონფიგურაციით დაემატება.</p>
          <button class="button button-primary" type="submit">გაგზავნა</button>
        </form>
      </section>
      <p class="empty-state" data-empty-state hidden>ასეთი საკონტაქტო ინფორმაცია ვერ მოიძებნა.</p>
    </section>
  </main>

<?php
render_donation_modal($bankAccounts);
render_footer($site);
