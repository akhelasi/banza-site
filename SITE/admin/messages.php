<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require_once __DIR__ . '/../includes/content-store.php';

require_admin();

$content = $contentStore ?? [];
$messages = visible_content_items($content['contactMessages'] ?? []);

function contact_message_index(array $messages, string $slug): ?int
{
    foreach ($messages as $index => $message) {
        if (($message['slug'] ?? '') === $slug) {
            return $index;
        }
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        admin_flash('უსაფრთხოების token არასწორია.', 'error');
        redirect('messages.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $slug = (string) ($_POST['slug'] ?? '');
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
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>სტატუსი</th><th>გამომგზავნი</th><th>სათაური</th><th>შეტყობინება</th><th>თარიღი</th><th>ქმედება</th></tr></thead>
        <tbody>
          <?php foreach ($messages as $message): ?>
            <tr>
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
  <?php endif; ?>
</section>

<?php render_admin_footer(); ?>
