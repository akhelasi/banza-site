<?php

declare(strict_types=1);

$siteRoot = dirname(__DIR__);

$options = getopt('', ['file::', 'get::']);
$file = (string) ($options['file'] ?? '');

if ($file === '') {
    $routes = [
        ['file' => 'about.php', 'get' => []],
        ['file' => 'history.php', 'get' => []],
        ['file' => 'football.php', 'get' => []],
        ['file' => 'contact.php', 'get' => []],
        ['file' => 'news-detail.php', 'get' => ['slug' => 'history-archive-seed']],
        ['file' => 'project-detail.php', 'get' => ['slug' => 'village-development-roadmap']],
    ];

    foreach ($routes as $route) {
        $command = escapeshellarg(PHP_BINARY)
            . ' ' . escapeshellarg(__FILE__)
            . ' --file=' . escapeshellarg($route['file'])
            . ' --get=' . escapeshellarg(json_encode($route['get'], JSON_UNESCAPED_SLASHES) ?: '{}');

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

$allowedFiles = ['about.php', 'history.php', 'football.php', 'contact.php', 'news-detail.php', 'project-detail.php'];
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
$_GET = $get;
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/' . $file;

ob_start();
include $siteRoot . '/' . $file;
$html = (string) ob_get_clean();

if (!str_contains($html, 'source-note')) {
    fwrite(STDERR, $file . " missing source-note\n");
    exit(1);
}

echo $file . " ok";
