<?php

declare(strict_types=1);

$siteRoot = dirname(__DIR__);

$options = getopt('', ['file::', 'get::', 'contains::']);
$file = (string) ($options['file'] ?? '');

if ($file === '') {
    $routes = [
        ['file' => 'index.php', 'get' => [], 'contains' => 'main-content'],
        ['file' => 'news.php', 'get' => [], 'contains' => 'main-content'],
        ['file' => 'projects.php', 'get' => [], 'contains' => 'main-content'],
        ['file' => 'about.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'history.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'football.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'contact.php', 'get' => [], 'contains' => 'source-note'],
        ['file' => 'news-detail.php', 'get' => ['slug' => 'history-archive-seed'], 'contains' => 'source-note'],
        ['file' => 'project-detail.php', 'get' => ['slug' => 'village-development-roadmap'], 'contains' => 'source-note'],
    ];

    foreach ($routes as $route) {
        $command = escapeshellarg(PHP_BINARY)
            . ' ' . escapeshellarg(__FILE__)
            . ' --file=' . escapeshellarg($route['file'])
            . ' --get=' . escapeshellarg(json_encode($route['get'], JSON_UNESCAPED_SLASHES) ?: '{}')
            . ' --contains=' . escapeshellarg($route['contains'])
            . ' 2>&1';

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);
        echo implode(PHP_EOL, $output) . PHP_EOL;

        if ($exitCode !== 0) {
            exit($exitCode);
        }
    }

    exit(0);
}

$allowedFiles = ['index.php', 'news.php', 'projects.php', 'about.php', 'history.php', 'football.php', 'contact.php', 'news-detail.php', 'project-detail.php'];
if (!in_array($file, $allowedFiles, true)) {
    fwrite(STDERR, "Unsupported file\n");
    exit(1);
}

$get = json_decode((string) ($options['get'] ?? '{}'), true);
if (!is_array($get)) {
    $get = [];
}

chdir($siteRoot);
ini_set('session.save_path', sys_get_temp_dir());
set_error_handler(static function (int $severity, string $message, string $filePath, int $line): bool {
    if ((error_reporting() & $severity) === 0) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $filePath, $line);
});

$_GET = $get;
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/' . $file;

ob_start();
include $siteRoot . '/' . $file;
$html = (string) ob_get_clean();

$needle = (string) ($options['contains'] ?? 'main-content');
if ($needle !== '' && !str_contains($html, $needle)) {
    fwrite(STDERR, $file . " missing {$needle}\n");
    exit(1);
}

echo $file . " ok";
