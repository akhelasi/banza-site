<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from CLI.\n");
    exit(1);
}

$options = getopt('', ['include-setup', 'include-routes', 'skip-js', 'help']);

if (array_key_exists('help', $options)) {
    fwrite(STDOUT, "Usage: php SITE/scripts/check-local-handoff.php [--include-setup] [--include-routes] [--skip-js]\n\n");
    fwrite(STDOUT, "Runs non-destructive local handoff checks without opening a production database connection.\n");
    fwrite(STDOUT, "Setup-production checks are opt-in because some sandboxed Codex environments can mutate script files while running that helper.\n");
    exit(0);
}

$siteRoot = dirname(__DIR__);
$projectRoot = dirname($siteRoot);
chdir($projectRoot);

$results = [];

function handoff_add_result(array &$results, string $name, int $exitCode, array $output): void
{
    $results[] = [
        'name' => $name,
        'exit_code' => $exitCode,
        'output' => $output,
    ];
}

function handoff_run(array &$results, string $name, array $command): void
{
    $commandLine = implode(' ', array_map('escapeshellarg', $command));
    $output = [];
    $exitCode = 0;
    exec($commandLine . ' 2>&1', $output, $exitCode);
    handoff_add_result($results, $name, $exitCode, $output);
}

function handoff_php_files(string $siteRoot): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($siteRoot, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file instanceof SplFileInfo && strtolower($file->getExtension()) === 'php') {
            $files[] = $file->getPathname();
        }
    }
    sort($files, SORT_STRING);

    return $files;
}

function handoff_run_php_lint(array &$results, string $siteRoot): void
{
    $errors = [];
    $checked = 0;

    foreach (handoff_php_files($siteRoot) as $file) {
        $output = [];
        $exitCode = 0;
        exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1', $output, $exitCode);
        $checked++;
        if ($exitCode !== 0) {
            $errors[] = $file . ': ' . implode(' ', $output);
        }
    }

    handoff_add_result(
        $results,
        'PHP syntax for all SITE PHP files',
        $errors === [] ? 0 : 1,
        $errors === [] ? ['Checked PHP files: ' . $checked] : $errors
    );
}

function handoff_run_json_parse(array &$results, string $siteRoot): void
{
    $path = $siteRoot . '/storage/content.json';
    $json = is_file($path) ? file_get_contents($path) : false;
    if ($json === false) {
        handoff_add_result($results, 'content.json parse', 1, ['SITE/storage/content.json is missing or unreadable.']);
        return;
    }

    $decoded = json_decode($json, true);
    if (!is_array($decoded)) {
        handoff_add_result($results, 'content.json parse', 1, ['Invalid JSON: ' . json_last_error_msg()]);
        return;
    }

    handoff_add_result($results, 'content.json parse', 0, ['content.json parsed successfully.']);
}

handoff_run_php_lint($results, $siteRoot);
handoff_run_json_parse($results, $siteRoot);

if (!array_key_exists('skip-js', $options)) {
    handoff_run($results, 'JavaScript syntax', ['node', '--check', 'SITE/assets/js/main.js']);
}

handoff_run($results, 'JSON-to-MySQL import dry-run', [PHP_BINARY, 'SITE/scripts/import-json-to-mysql.php', '--dry-run', '--only=all']);
handoff_run($results, 'Launch readiness handoff mode', [PHP_BINARY, 'SITE/scripts/check-launch-readiness.php']);

if (array_key_exists('include-setup', $options)) {
    handoff_run($results, 'Migration dry-run', [PHP_BINARY, 'SITE/scripts/setup-production.php', '--migrate', '--dry-run']);
    handoff_run($results, 'Launch content audit handoff mode', [PHP_BINARY, 'SITE/scripts/setup-production.php', '--audit-content', '--allow-open']);
} else {
    handoff_add_result($results, 'Migration dry-run', 0, ['Skipped by default. Run with --include-setup in a normal terminal or host preview.']);
    handoff_add_result($results, 'Launch content audit handoff mode', 0, ['Skipped by default. Run with --include-setup in a normal terminal or host preview.']);
}

if (array_key_exists('include-routes', $options)) {
    handoff_run($results, 'Public route smoke', [PHP_BINARY, 'SITE/scripts/setup-production.php', '--check-routes']);
} else {
    handoff_add_result($results, 'Public route smoke', 0, ['Skipped by default. Run with --include-routes in a normal terminal or host preview.']);
}

$failed = array_values(array_filter($results, static fn (array $result): bool => (int) $result['exit_code'] !== 0));

fwrite(STDOUT, "Local handoff check\n");
fwrite(STDOUT, 'Checks: ' . count($results) . ' | Failed: ' . count($failed) . PHP_EOL);

foreach ($results as $result) {
    $status = ((int) $result['exit_code']) === 0 ? 'OK' : 'FAILED';
    fwrite(STDOUT, '[' . $status . '] ' . $result['name'] . PHP_EOL);
    foreach ($result['output'] as $line) {
        fwrite(STDOUT, '  ' . $line . PHP_EOL);
    }
}

if ($failed !== []) {
    fwrite(STDERR, "Local handoff checks failed.\n");
    exit(1);
}

exit(0);
