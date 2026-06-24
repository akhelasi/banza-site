<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require_once __DIR__ . '/../includes/content-store.php';
require __DIR__ . '/../includes/repositories/contact-message-repository.php';

require_admin();

$content = $contentStore ?? [];
$useMysqlMessages = content_storage_driver() === 'mysql';
$messages = $useMysqlMessages ? fetch_contact_messages_from_mysql(db()) : visible_content_items($content['contactMessages'] ?? []);

function contact_message_index(array $messages, string $slug): ?int
{
    foreach ($messages as $index => $message) {
        if (($message['slug'] ?? '') === $slug) {
            return $index;
        }
    }

    return null;
}

function posted_message_slugs(): array
{
    $slugs = $_POST['slugs'] ?? [];
    if (!is_array($slugs)) {
        return [];
    }

    return array_values(array_unique(array_filter(array_map(static function (mixed $slug): string {
        return trim((string) $slug);
    }, $slugs))));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        admin_flash('უსაფრთხოების token არასწორია.', 'error');
        redirect('messages.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $slug = (string) ($_POST['slug'] ?? '');

    if (in_array($action, ['bulk_mark_read', 'bulk_soft_delete'], true)) {
        $selectedSlugs = posted_message_slugs();

        if ($selectedSlugs === []) {
            admin_flash('აირჩიეთ მინიმუმ ერთი შეტყობინება.', 'error');
            redirect('messages.php');
        }

        $changedCount = 0;
        if ($useMysqlMessages) {
            foreach ($selectedSlugs as $selectedSlug) {
                $changed = $action === 'bulk_mark_read'
                    ? mark_contact_message_read_in_mysql(db(), $selectedSlug)
                    : soft_delete_contact_message_in_mysql(db(), $selectedSlug);
                $changedCount += $changed ? 1 : 0;
            }
        } else {
            foreach (($content['contactMessages'] ?? []) as $index => $message) {
                if (!in_array((string) ($message['slug'] ?? ''), $selectedSlugs, true) || !empty($message['deleted_at'])) {
                    continue;
                }

                if ($action === 'bulk_mark_read') {
                    $content['contactMessages'][$index]['read_at'] = date('c');
                } else {
                    $content['contactMessages'][$index]['deleted_at'] = date('c');
                }
                $content['contactMessages'][$index] = touch_content_dates($content['contactMessages'][$index]);
                $changedCount++;
            }

            if ($changedCount > 0) {
                save_content_store($content);
            }
        }

        if ($changedCount === 0) {
            admin_flash('არჩეული შეტყობინებები ვერ მოიძებნა ან უკვე დამუშავებულია.', 'error');
            redirect('messages.php');
        }

        admin_flash($changedCount . ' შეტყობინება დამუშავდა.');
        redirect('messages.php');
    }

    if ($useMysqlMessages) {
        $message = find_contact_message_in_mysql(db(), $slug);
        if ($message === null) {
            admin_flash('შეტყობინება ვერ მოიძებნა.', 'error');
            redirect('messages.php');
        }

        if ($action === 'mark_read') {
            mark_contact_message_read_in_mysql(db(), $slug);
            admin_flash('შეტყობინება მოინიშნა წაკითხულად.');
            redirect('messages.php');
        }

        if ($action === 'soft_delete') {
            soft_delete_contact_message_in_mysql(db(), $slug);
            admin_flash('შეტყობინება გადავიდა სანაგვეში.');
            redirect('messages.php');
        }

        admin_flash('ქმედება არასწორია.', 'error');
        redirect('messages.php');
    }

    $index = contact_message_index($content['contactMessages'] ?? [], $slug);

    if ($index === null || !empty($content['contactMessages'][$index]['deleted_at'])) {
        admin_flash('შეტყობინება ვერ მოიძებნა.', 'error');
        redirect('messages.php');
    }

    if ($action === 'mark_read') {
        $content['contactMessages'][$index]['read_at'] = date('c');
        $content['contactMessages'][$index] = touch_content_dates($content['contactMessages'][$index]);
        save_content_store($content);
        admin_flash('შეტყობინება მოინიშნა წაკითხულად.');
        redirect('messages.php');
    }

    if ($action === 'soft_delete') {
        $content['contactMessages'][$index]['deleted_at'] = date('c');
        $content['contactMessages'][$index] = touch_content_dates($content['contactMessages'][$index]);
        save_content_store($content);
        admin_flash('შეტყობინება გადავიდა სანაგვეში.');
        redirect('messages.php');
    }

    admin_flash('ქმედება არასწორია.', 'error');
    redirect('messages.php');
}

usort($messages, static fn (array $a, array $b): int => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));
$unreadCount = count(array_filter($messages, static fn (array $message): bool => empty($message['read_at'])));

render_admin_header('შეტყობინებები', 'messages');
?>

<section class="admin-card">
  <div class="admin-card-heading">
    <div>
      <p class="eyebrow">Contact inbox</p>
      <h2>საკონტაქტო შეტყობინებები</h2>
    </div>
    <span><?php echo count($messages); ?> სულ / <?php echo $unreadCount; ?> ახალი</span>
  </div>

  <?php if ($messages === []): ?>
    <div class="empty-admin-state">
      <h3>შეტყობინებები ჯერ არ არის</h3>
      <p>საიტიდან გამოგზავნილი საკონტაქტო ფორმები აქ გამოჩნდება.</p>
    </div>
  <?php else: ?>
    <form class="filter-bar has-sort admin-filter" data-live-filter data-filter-target="#adminMessagesList" aria-label="შეტყობინებების ძებნა, ფილტრი და დალაგება">
      <label><span>ძებნა</span><input type="search" name="search" placeholder="სახელი, ელფოსტა, სათაური ან ტექსტი"></label>
      <label>
        <span>სტატუსი</span>
        <select name="category">
          <option value="">ყველა</option>
          <option value="new">ახალი</option>
          <option value="read">წაკითხული</option>
        </select>
      </label>
      <label>
        <span>დალაგება</span>
        <select name="sort">
          <option value="date-desc">თარიღი: ახალი ჯერ</option>
          <option value="date-asc">თარიღი: ძველი ჯერ</option>
          <option value="title-asc">გამომგზავნი: ზრდადობით</option>
          <option value="title-desc">გამომგზავნი: კლებადობით</option>
        </select>
      </label>
    </form>
    <form id="bulkMessagesForm" class="admin-bulk-bar" method="post">
      <?php echo csrf_field(); ?>
      <button type="submit" name="action" value="bulk_mark_read">მონიშნულის წაკითხულად მონიშვნა</button>
      <button type="submit" name="action" value="bulk_soft_delete" onclick="return confirm('არჩეული შეტყობინებები გადავიდეს სანაგვეში?');">მონიშნულის წაშლა</button>
    </form>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>არჩევა</th><th>სტატუსი</th><th>გამომგზავნი</th><th>სათაური</th><th>შეტყობინება</th><th>თარიღი</th><th>ქმედება</th></tr></thead>
        <tbody id="adminMessagesList">
          <?php foreach ($messages as $message): ?>
            <?php $messageStatus = empty($message['read_at']) ? 'new' : 'read'; ?>
            <tr class="filter-item" data-title="<?php echo e($message['name'] ?? ''); ?>" data-text="<?php echo e(($message['email'] ?? '') . ' ' . ($message['phone'] ?? '') . ' ' . ($message['subject'] ?? '') . ' ' . ($message['message'] ?? '')); ?>" data-category="<?php echo e($messageStatus); ?>" data-sort-title="<?php echo e($message['name'] ?? ''); ?>" data-sort-date="<?php echo e($message['created_at'] ?? ''); ?>">
              <td><input type="checkbox" name="slugs[]" value="<?php echo e($message['slug'] ?? ''); ?>" form="bulkMessagesForm" aria-label="შეტყობინების მონიშვნა: <?php echo e($message['subject'] ?? ''); ?>"></td>
              <td><span class="status-pill <?php echo empty($message['read_at']) ? 'status-new' : 'status-read'; ?>"><?php echo empty($message['read_at']) ? 'ახალი' : 'წაკითხული'; ?></span></td>
              <td>
                <strong><?php echo e($message['name'] ?? ''); ?></strong>
                <small><?php echo e($message['email'] ?? ''); ?></small>
                <?php if (!empty($message['phone'])): ?><small><?php echo e($message['phone']); ?></small><?php endif; ?>
              </td>
              <td><?php echo e($message['subject'] ?? ''); ?></td>
              <td class="message-preview"><?php echo e($message['message'] ?? ''); ?></td>
              <td><?php echo e($message['created_at'] ?? ''); ?></td>
              <td class="admin-actions">
                <?php if (empty($message['read_at'])): ?>
                  <form method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="mark_read">
                    <input type="hidden" name="slug" value="<?php echo e($message['slug'] ?? ''); ?>">
                    <button type="submit">წაკითხულად მონიშვნა</button>
                  </form>
                <?php endif; ?>
                <form method="post">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="soft_delete">
                  <input type="hidden" name="slug" value="<?php echo e($message['slug'] ?? ''); ?>">
                  <button type="submit">წაშლა</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="empty-state" data-empty-state hidden>ასეთი შეტყობინება ვერ მოიძებნა.</p>
  <?php endif; ?>
</section>

<?php render_admin_footer(); ?>
