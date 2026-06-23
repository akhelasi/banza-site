<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require_once __DIR__ . '/../includes/content-store.php';

require_admin();

$content = $contentStore ?? [];

function parse_social_links(string $value): array
{
    $items = [];
    foreach (split_lines($value) as $line) {
        $parts = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
        if ($parts[0] === '' || $parts[1] === '') {
            continue;
        }
        $items[] = ['label' => $parts[0], 'href' => $parts[1], 'icon' => $parts[2] !== '' ? $parts[2] : mb_substr($parts[0], 0, 1)];
    }

    return $items;
}

function social_links_text(array $items): string
{
    return implode(PHP_EOL, array_map(static function (array $item): string {
        return trim(($item['label'] ?? '') . ' | ' . ($item['href'] ?? '') . ' | ' . ($item['icon'] ?? ''));
    }, $items));
}

function parse_bank_accounts(string $value): array
{
    $items = [];
    foreach (split_lines($value) as $line) {
        $parts = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
        if ($parts[0] === '' || $parts[1] === '') {
            continue;
        }
        $items[] = ['bank' => $parts[0], 'account' => $parts[1], 'note' => $parts[2]];
    }

    return $items;
}

function bank_accounts_text(array $items): string
{
    return implode(PHP_EOL, array_map(static function (array $item): string {
        return trim(($item['bank'] ?? '') . ' | ' . ($item['account'] ?? '') . ' | ' . ($item['note'] ?? ''));
    }, $items));
}

function parse_nearby_weather(string $value): array
{
    $items = [];
    foreach (split_lines($value) as $line) {
        $parts = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');
        if ($parts[0] === '' || $parts[2] === '') {
            continue;
        }
        $items[] = ['name' => $parts[0], 'forecast' => $parts[1], 'temperature' => $parts[2]];
    }

    return $items;
}

function nearby_weather_text(array $items): string
{
    return implode(PHP_EOL, array_map(static function (array $item): string {
        return trim(($item['name'] ?? '') . ' | ' . ($item['forecast'] ?? '') . ' | ' . ($item['temperature'] ?? ''));
    }, $items));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        admin_flash('უსაფრთხოების token არასწორია.', 'error');
        redirect('settings.php');
    }

    $social = parse_social_links((string) ($_POST['social_links'] ?? ''));
    $banks = parse_bank_accounts((string) ($_POST['bank_accounts'] ?? ''));
    $nearby = parse_nearby_weather((string) ($_POST['nearby_weather'] ?? ''));

    if ($social === [] || $banks === []) {
        admin_flash('Social links და donation accounts მინიმუმ ერთი ჩანაწერით უნდა იყოს შევსებული.', 'error');
        redirect('settings.php');
    }

    $content['socialLinks'] = $social;
    $content['bankAccounts'] = $banks;
    $content['camera'] = [
        'title' => trim((string) ($_POST['camera_title'] ?? 'ლაივ კამერა')),
        'status' => trim((string) ($_POST['camera_status'] ?? 'LIVE demo')),
        'preview_image' => trim((string) ($_POST['camera_preview_image'] ?? '')),
        'description' => trim((string) ($_POST['camera_description'] ?? '')),
    ];
    $content['weather'] = [
        'summary' => trim((string) ($_POST['weather_summary'] ?? '')),
        'temperature' => trim((string) ($_POST['weather_temperature'] ?? '')),
        'wind' => trim((string) ($_POST['weather_wind'] ?? '')),
        'humidity' => trim((string) ($_POST['weather_humidity'] ?? '')),
        'rain' => trim((string) ($_POST['weather_rain'] ?? '')),
        'nearby' => $nearby,
    ];

    save_content_store($content);
    admin_flash('პარამეტრები შენახულია.');
    redirect('settings.php');
}

render_admin_header('პარამეტრები', 'settings');
?>

<section class="admin-card">
  <h2>საიტის პარამეტრები</h2>
  <p>აქედან იმართება social links, donation accounts, live camera და weather demo/config მონაცემები.</p>
  <form class="admin-form" method="post">
    <?php echo csrf_field(); ?>

    <label><span>Social links: label | url | icon</span><textarea name="social_links" rows="5" required><?php echo e(social_links_text($socialLinks)); ?></textarea></label>
    <label><span>Donation accounts: bank | account | note</span><textarea name="bank_accounts" rows="5" required><?php echo e(bank_accounts_text($bankAccounts)); ?></textarea></label>

    <fieldset class="admin-fieldset">
      <legend>ლაივ კამერა</legend>
      <div class="admin-form-grid">
        <label><span>სათაური</span><input type="text" name="camera_title" value="<?php echo e($camera['title'] ?? ''); ?>"></label>
        <label><span>სტატუსი</span><input type="text" name="camera_status" value="<?php echo e($camera['status'] ?? ''); ?>"></label>
      </div>
      <label><span>Preview image URL/path</span><input type="text" name="camera_preview_image" value="<?php echo e($camera['preview_image'] ?? ''); ?>"></label>
      <label><span>აღწერა</span><textarea name="camera_description" rows="3"><?php echo e($camera['description'] ?? ''); ?></textarea></label>
    </fieldset>

    <fieldset class="admin-fieldset">
      <legend>ამინდი</legend>
      <div class="admin-form-grid">
        <label><span>აღწერა</span><input type="text" name="weather_summary" value="<?php echo e($weather['summary'] ?? ''); ?>"></label>
        <label><span>ტემპერატურა</span><input type="text" name="weather_temperature" value="<?php echo e($weather['temperature'] ?? ''); ?>"></label>
        <label><span>ქარი</span><input type="text" name="weather_wind" value="<?php echo e($weather['wind'] ?? ''); ?>"></label>
        <label><span>ტენიანობა</span><input type="text" name="weather_humidity" value="<?php echo e($weather['humidity'] ?? ''); ?>"></label>
        <label><span>წვიმის ალბათობა</span><input type="text" name="weather_rain" value="<?php echo e($weather['rain'] ?? ''); ?>"></label>
      </div>
      <label><span>ახლომდებარე ადგილები: name | forecast | temperature</span><textarea name="nearby_weather" rows="5"><?php echo e(nearby_weather_text($weather['nearby'] ?? [])); ?></textarea></label>
    </fieldset>

    <button class="button button-primary" type="submit">შენახვა</button>
  </form>
</section>

<?php render_admin_footer(); ?>