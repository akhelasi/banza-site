<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require_once __DIR__ . '/../includes/content-store.php';

require_admin();

$content = $contentStore ?? [];

function trash_item_index(array $items, string $slug): ?int
{
    foreach ($items as $index => $item) {
        if (($item['slug'] ?? '') === $slug) {
            return $index;
        }
    }

    return null;
}

function deleted_items(array $content): array
{
    $deleted = [];
    foreach (['news' => 'ახალი ამბავი', 'projects' => 'პროექტი', 'contactMessages' => 'შეტყობინება'] as $key => $label) {
        foreach (($content[$key] ?? []) as $item) {
            if (!empty($item['deleted_at'])) {
                $item['_content_key'] = $key;
                $item['_type_label'] = $label;
                $deleted[] = $item;
            }
        }
    }

    usort($deleted, static fn (array $a, array $b): int => strcmp((string) ($b['deleted_at'] ?? ''), (string) ($a['deleted_at'] ?? '')));
    return $deleted;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        admin_flash('უსაფრთხოების token არასწორია.', 'error');
        redirect('trash.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $key = (string) ($_POST['content_key'] ?? '');
    $slug = (string) ($_POST['slug'] ?? '');

    if (!in_array($key, ['news', 'projects', 'contactMessages'], true)) {
        admin_flash('კონტენტის ტიპი არასწორია.', 'error');
        redirect('trash.php');
    }

    $index = trash_item_index($content[$key] ?? [], $slug);
    if ($index === null || empty($content[$key][$index]['deleted_at'])) {
        admin_flash('წაშლილი ჩანაწერი ვერ მოიძებნა.', 'error');
        redirect('trash.php');
    }

    if ($action === 'restore') {
        $content[$key][$index]['deleted_at'] = '';
        $content[$key][$index] = touch_content_dates($content[$key][$index]);
        save_content_store($content);
        admin_flash('ჩანაწერი აღდგენილია.');
        redirect('trash.php');
    }

    if ($action === 'permanent_delete') {
        $item = $content[$key][$index];
        $paths = collect_upload_paths($item);
        array_splice($content[$key], $index, 1);
        save_content_store($content);

        $deletedFiles = 0;
        foreach ($paths as $path) {
            if (delete_uploaded_file_if_unreferenced($path, $content)) {
                $deletedFiles += 1;
            }
        }

        admin_flash('ჩანაწერი პერმანენტულად წაიშალა.' . ($deletedFiles > 0 ? ' წაშლილი ფაილები: ' . $deletedFiles : ''));
        redirect('trash.php');
    }

    admin_flash('ქმედება არასწორია.', 'error');
    redirect('trash.php');
}

render_admin_header('სანაგვე', 'trash');
$items = deleted_items($content);
?>

<section class="admin-card">
  <div class="admin-card-heading">
    <div>
      <p class="eyebrow">Soft delete</p>
      <h2>სანაგვე</h2>
    </div>
    <span><?php echo count($items); ?> ჩანაწერი</span>
  </div>

  <?php if ($items === []): ?>
    <div class="empty-admin-state">
      <h3>სანაგვე ცარიელია</h3>
      <p>წაშლილი სიახლეები, პროექტები და საკონტაქტო შეტყობინებები აქ გამოჩნდება restore და permanent delete მოქმედებებით.</p>
    </div>
  <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>ტიპი</th><th>სათაური</th><th>წაშლის დრო</th><th>ფაილები</th><th>ქმედება</th></tr></thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?php echo e($item['_type_label']); ?></td>
              <td><?php echo e($item['title'] ?? $item['subject'] ?? $item['name'] ?? ''); ?><small><?php echo e($item['slug'] ?? ''); ?></small></td>
              <td><?php echo e($item['deleted_at'] ?? ''); ?></td>
              <td><?php echo count(collect_upload_paths($item)); ?></td>
              <td class="admin-actions">
                <form method="post">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="restore">
                  <input type="hidden" name="content_key" value="<?php echo e($item['_content_key']); ?>">
                  <input type="hidden" name="slug" value="<?php echo e($item['slug'] ?? ''); ?>">
                  <button type="submit">აღდგენა</button>
                </form>
                <form method="post" onsubmit="return confirm('პერმანენტულად წაიშალოს? ეს მოქმედება ვერ დაბრუნდება.');">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="action" value="permanent_delete">
                  <input type="hidden" name="content_key" value="<?php echo e($item['_content_key']); ?>">
                  <input type="hidden" name="slug" value="<?php echo e($item['slug'] ?? ''); ?>">
                  <button type="submit">პერმანენტულად წაშლა</button>
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
