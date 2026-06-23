<?php

declare(strict_types=1);

function db_config(): array
{
    $localConfig = __DIR__ . '/config.php';
    $exampleConfig = __DIR__ . '/config.example.php';

    return require file_exists($localConfig) ? $localConfig : $exampleConfig;
}

function content_storage_driver(): string
{
    $driver = strtolower(trim((string) (db_config()['content_storage']['driver'] ?? 'json')));
    return in_array($driver, ['json', 'mysql'], true) ? $driver : 'json';
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = db_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['name'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
