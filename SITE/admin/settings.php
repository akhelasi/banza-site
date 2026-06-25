<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/data.php';
require __DIR__ . '/../includes/admin-layout.php';
require_once __DIR__ . '/../includes/content-store.php';

require_admin();

$content = $contentStore ?? [];
$weather = is_array($content['weather'] ?? null) ? $content['weather'] : $weather;
$notifications = is_array($content['notifications'] ?? null) ? $content['notifications'] : ($notifications ?? []);

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
    $notificationEnabled = !empty($_POST['notifications_enabled']);
    $notificationRecipient = trim((string) ($_POST['notification_recipient_email'] ?? ''));
    $notificationFrom = trim((string) ($_POST['notification_from_email'] ?? ''));

    if ($social === [] || $banks === []) {
        admin_flash('Social links და donation accounts მინიმუმ ერთი ჩანაწერით უნდა იყოს შევსებული.', 'error');
        redirect('settings.php');
    }

    if ($notificationEnabled && !filter_var($notificationRecipient, FILTER_VALIDATE_EMAIL)) {
        admin_flash('Notification recipient email is required when email notifications are enabled.', 'error');
        redirect('settings.php');
    }

    if ($notificationFrom !== '' && !filter_var($notificationFrom, FILTER_VALIDATE_EMAIL)) {
        admin_flash('Notification from email must be valid.', 'error');
        redirect('settings.php');
    }

    $content['socialLinks'] = $social;
    $content['bankAccounts'] = $banks;
    $content['camera'] = [
        'title' => trim((string) ($_POST['camera_title'] ?? 'ლაივ კამერა')),
        'status' => trim((string) ($_POST['camera_status'] ?? 'LIVE demo')),
        'preview_image' => trim((string) ($_POST['camera_preview_image'] ?? '')),
        'stream_url' => trim((string) ($_POST['camera_stream_url'] ?? '')),
        'description' => trim((string) ($_POST['camera_description'] ?? '')),
    ];
    $weatherProvider = (string) ($_POST['weather_provider'] ?? 'open_meteo');
    if (!in_array($weatherProvider, ['open_meteo', 'demo'], true)) {
        $weatherProvider = 'open_meteo';
    }

    $content['weather'] = [
        'summary' => trim((string) ($_POST['weather_summary'] ?? '')),
        'temperature' => trim((string) ($_POST['weather_temperature'] ?? '')),
        'wind' => trim((string) ($_POST['weather_wind'] ?? '')),
        'humidity' => trim((string) ($_POST['weather_humidity'] ?? '')),
        'rain' => trim((string) ($_POST['weather_rain'] ?? '')),
        'nearby' => $nearby,
        'live' => [
            'provider' => $weatherProvider,
            'enabled' => $weatherProvider === 'open_meteo',
            'latitude' => (float) ($_POST['weather_latitude'] ?? 42.34889),
            'longitude' => (float) ($_POST['weather_longitude'] ?? 42.28417),
            'cache_minutes' => max(5, (int) ($_POST['weather_cache_minutes'] ?? 30)),
        ],
    ];

    $content['notifications'] = [
        'enabled' => $notificationEnabled,
        'recipient_email' => $notificationRecipient,
        'from_email' => $notificationFrom,
        'subject_prefix' => trim((string) ($_POST['notification_subject_prefix'] ?? '[Banza Site]')),
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
      <label><span>Stream embed URL</span><input type="url" name="camera_stream_url" value="<?php echo e($camera['stream_url'] ?? ''); ?>"></label>
      <label><span>აღწერა</span><textarea name="camera_description" rows="3"><?php echo e($camera['description'] ?? ''); ?></textarea></label>
    </fieldset>

    <fieldset class="admin-fieldset">
      <legend>ამინდი</legend>
      <?php $weatherLive = is_array($weather['live'] ?? null) ? $weather['live'] : []; ?>
      <div class="admin-form-grid">
        <label><span>Provider</span>
          <select name="weather_provider">
            <option value="open_meteo"<?php echo (($weatherLive['provider'] ?? 'open_meteo') === 'open_meteo') ? ' selected' : ''; ?>>Open-Meteo live</option>
            <option value="demo"<?php echo (($weatherLive['provider'] ?? '') === 'demo') ? ' selected' : ''; ?>>Admin fallback only</option>
          </select>
        </label>
        <label><span>Cache minutes</span><input type="number" min="5" name="weather_cache_minutes" value="<?php echo e($weatherLive['cache_minutes'] ?? 30); ?>"></label>
        <label><span>Latitude</span><input type="number" step="0.00001" name="weather_latitude" value="<?php echo e($weatherLive['latitude'] ?? 42.34889); ?>"></label>
        <label><span>Longitude</span><input type="number" step="0.00001" name="weather_longitude" value="<?php echo e($weatherLive['longitude'] ?? 42.28417); ?>"></label>
        <label><span>აღწერა</span><input type="text" name="weather_summary" value="<?php echo e($weather['summary'] ?? ''); ?>"></label>
        <label><span>ტემპერატურა</span><input type="text" name="weather_temperature" value="<?php echo e($weather['temperature'] ?? ''); ?>"></label>
        <label><span>ქარი</span><input type="text" name="weather_wind" value="<?php echo e($weather['wind'] ?? ''); ?>"></label>
        <label><span>ტენიანობა</span><input type="text" name="weather_humidity" value="<?php echo e($weather['humidity'] ?? ''); ?>"></label>
        <label><span>წვიმის ალბათობა</span><input type="text" name="weather_rain" value="<?php echo e($weather['rain'] ?? ''); ?>"></label>
      </div>
      <label><span>ახლომდებარე ადგილები: name | forecast | temperature</span><textarea name="nearby_weather" rows="5"><?php echo e(nearby_weather_text($weather['nearby'] ?? [])); ?></textarea></label>
    </fieldset>

    <button class="button button-primary" type="submit">შენახვა</button>
    <fieldset class="admin-fieldset">
      <legend>Email notifications</legend>
      <label class="checkbox-label"><input type="checkbox" name="notifications_enabled" value="1"<?php echo !empty($notifications['enabled']) ? ' checked' : ''; ?>> Contact form email notification enabled</label>
      <div class="admin-form-grid">
        <label><span>Recipient email</span><input type="email" name="notification_recipient_email" value="<?php echo e($notifications['recipient_email'] ?? ''); ?>"></label>
        <label><span>From email</span><input type="email" name="notification_from_email" value="<?php echo e($notifications['from_email'] ?? ''); ?>"></label>
        <label><span>Subject prefix</span><input type="text" name="notification_subject_prefix" value="<?php echo e($notifications['subject_prefix'] ?? '[Banza Site]'); ?>"></label>
      </div>
      <p class="form-hint">If email delivery fails, the contact message is still saved in the admin inbox.</p>
    </fieldset>
  </form>
</section>

<?php render_admin_footer(); ?>
