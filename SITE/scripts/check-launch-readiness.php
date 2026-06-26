<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(1);
}

$options = getopt('', ['strict', 'help']);

if (array_key_exists('help', $options)) {
    fwrite(STDOUT, "Usage: php SITE/scripts/check-launch-readiness.php [--strict]\n\n");
    fwrite(STDOUT, "Checks launch-critical admin coverage, local content data and known handoff blockers.\n");
    fwrite(STDOUT, "Default exit code fails only on BLOCKER items. --strict also fails on WARN/WAITING items.\n");
    exit(0);
}

$strict = array_key_exists('strict', $options);
$siteRoot = dirname(__DIR__);
$projectRoot = dirname($siteRoot);
$checks = [];

function readiness_path(string $relative): string
{
    global $siteRoot;
    return $siteRoot . '/' . str_replace('\\', '/', $relative);
}

function readiness_project_path(string $relative): string
{
    global $projectRoot;
    return $projectRoot . '/' . str_replace('\\', '/', $relative);
}

function readiness_add(array &$checks, string $level, string $key, string $message): void
{
    $checks[] = ['level' => $level, 'key' => $key, 'message' => $message];
}

function readiness_file_contains_all(string $relative, array $needles, array &$missing): bool
{
    $path = readiness_path($relative);
    if (!is_file($path)) {
        $missing[] = 'file missing';
        return false;
    }

    $contents = file_get_contents($path);
    if (!is_string($contents)) {
        $missing[] = 'file unreadable';
        return false;
    }

    foreach ($needles as $needle) {
        if (!str_contains($contents, $needle)) {
            $missing[] = $needle;
        }
    }

    return $missing === [];
}

function readiness_count_visible(array $items): int
{
    return count(array_filter($items, static fn (array $item): bool => empty($item['deleted_at'])));
}

$capabilityChecks = [
    'admin.content.news_projects_pages' => [
        'file' => 'admin/content.php',
        'needles' => ["'news', 'projects', 'pages'", "save_page", "main_image", "gallery_images", "videos", "bulk_delete", "source_status", "admin_preview_url"],
        'message' => 'Admin content page manages news, projects, static pages, uploads, videos, source status, bulk delete and previews.',
    ],
    'admin.settings.site_integrations' => [
        'file' => 'admin/settings.php',
        'needles' => ['social_links', 'bank_accounts', 'camera_stream_url', 'weather_provider', 'notifications_enabled'],
        'message' => 'Admin settings page manages social links, donation accounts, camera, weather and contact notifications.',
    ],
    'admin.media.library' => [
        'file' => 'admin/media.php',
        'needles' => ['store_uploaded_image', 'data-live-filter', 'alt', 'caption', 'seedMediaList'],
        'message' => 'Admin media page supports uploads, search/filter/sort and uploaded image metadata.',
    ],
    'admin.messages.inbox' => [
        'file' => 'admin/messages.php',
        'needles' => ['bulk_mark_read', 'bulk_soft_delete', 'mark_read', 'soft_delete', 'data-live-filter'],
        'message' => 'Admin messages page supports inbox review, mark-read, soft-delete and bulk actions.',
    ],
    'admin.trash.lifecycle' => [
        'file' => 'admin/trash.php',
        'needles' => ['restore', 'permanent_delete', 'delete_uploaded_file_if_unreferenced', 'data-live-filter'],
        'message' => 'Admin trash supports restore, permanent delete and unreferenced upload cleanup.',
    ],
    'admin.profile.credentials' => [
        'file' => 'admin/profile.php',
        'needles' => ['write_admin_credentials', 'current_password', 'new_password'],
        'message' => 'Admin profile page supports password/profile updates for the active credential source.',
    ],
];

foreach ($capabilityChecks as $key => $check) {
    $missing = [];
    if (readiness_file_contains_all($check['file'], $check['needles'], $missing)) {
        readiness_add($checks, 'OK', $key, $check['message']);
    } else {
        readiness_add($checks, 'BLOCKER', $key, 'Missing expected admin coverage in ' . $check['file'] . ': ' . implode(', ', $missing));
    }
}

$contentPath = readiness_path('storage/content.json');
$content = [];
if (!is_file($contentPath)) {
    readiness_add($checks, 'BLOCKER', 'content.storage', 'SITE/storage/content.json is missing.');
} else {
    $rawJson = file_get_contents($contentPath);
    $decoded = is_string($rawJson) ? json_decode($rawJson, true) : null;
    if (!is_array($decoded)) {
        readiness_add($checks, 'BLOCKER', 'content.storage', 'SITE/storage/content.json is invalid JSON: ' . json_last_error_msg());
    } else {
        $content = $decoded;
        readiness_add($checks, 'OK', 'content.storage', 'SITE/storage/content.json is readable JSON.');
    }
}

if ($content !== []) {
    $requiredSections = ['news', 'projects', 'about', 'history', 'football', 'contact', 'socialLinks', 'bankAccounts', 'camera', 'weather'];
    foreach ($requiredSections as $section) {
        if (!array_key_exists($section, $content)) {
            readiness_add($checks, 'BLOCKER', 'content.' . $section, 'Required content section is missing.');
            continue;
        }

        readiness_add($checks, 'OK', 'content.' . $section, 'Required content section exists.');
    }

    if (readiness_count_visible($content['news'] ?? []) < 1) {
        readiness_add($checks, 'BLOCKER', 'content.news.items', 'At least one visible news item is required for launch.');
    }

    if (readiness_count_visible($content['projects'] ?? []) < 1) {
        readiness_add($checks, 'BLOCKER', 'content.projects.items', 'At least one visible project item is required for launch.');
    }

    if (($content['socialLinks'] ?? []) === []) {
        readiness_add($checks, 'BLOCKER', 'content.social_links.items', 'At least one social link is required.');
    }

    if (($content['bankAccounts'] ?? []) === []) {
        readiness_add($checks, 'BLOCKER', 'content.bank_accounts.items', 'At least one donation account is required.');
    }

    $demoNeedles = ['demo', 'Demo', 'GE00', 'facebook.com/', 'instagram.com/', 'youtube.com/', '+995 000', 'info@banza.ge'];
    $flatContent = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    foreach ($demoNeedles as $needle) {
        if (str_contains($flatContent, $needle)) {
            readiness_add($checks, 'WAITING', 'content.client_approval', 'Demo or generic content remains: ' . $needle);
            break;
        }
    }
}

if (is_dir(readiness_path('uploads'))) {
    readiness_add($checks, 'OK', 'uploads.directory', 'SITE/uploads exists.');
} else {
    readiness_add($checks, 'BLOCKER', 'uploads.directory', 'SITE/uploads is missing.');
}

if (is_file(readiness_path('includes/config.php'))) {
    readiness_add($checks, 'WARN', 'config.production', 'Untracked production config exists locally. Confirm it contains no demo credentials before deployment.');
} else {
    readiness_add($checks, 'WAITING', 'config.production', 'Production config is not present in this repo, which is correct for Git; create it on the host.');
}

readiness_add($checks, 'WAITING', 'hosting.provider', 'Production PHP/MySQL hosting and domain details are still required.');
readiness_add($checks, 'WAITING', 'manual.browser_qa', 'Manual desktop/tablet/mobile browser QA must still be completed outside this sandbox.');
readiness_add($checks, 'WAITING', 'mysql.smoke', 'A real MySQL import/login/profile smoke test is still required on the target host or dev database.');

$rank = ['BLOCKER' => 0, 'WARN' => 1, 'WAITING' => 2, 'OK' => 3];
usort($checks, static function (array $a, array $b) use ($rank): int {
    return ($rank[$a['level']] ?? 9) <=> ($rank[$b['level']] ?? 9) ?: strcmp($a['key'], $b['key']);
});

$counts = ['OK' => 0, 'WARN' => 0, 'WAITING' => 0, 'BLOCKER' => 0];
foreach ($checks as $check) {
    $counts[$check['level']] = ($counts[$check['level']] ?? 0) + 1;
}

fwrite(STDOUT, "Launch readiness check\n");
fwrite(STDOUT, 'OK: ' . $counts['OK'] . ' | WARN: ' . $counts['WARN'] . ' | WAITING: ' . $counts['WAITING'] . ' | BLOCKER: ' . $counts['BLOCKER'] . PHP_EOL);

foreach ($checks as $check) {
    fwrite(STDOUT, '[' . $check['level'] . '] ' . $check['key'] . ': ' . $check['message'] . PHP_EOL);
}

if ($counts['BLOCKER'] > 0) {
    exit(1);
}

if ($strict && ($counts['WARN'] > 0 || $counts['WAITING'] > 0)) {
    exit(1);
}

exit(0);