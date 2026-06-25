<?php

declare(strict_types=1);

function ensure_session(): void
{
    if (function_exists('app_session_start')) {
        app_session_start();
        return;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function admin_credentials(): array
{
    $config = db_config();
    return $config['admin'] ?? [];
}

function admin_record_from_mysql_by_email(string $email): ?array
{
    if (content_storage_driver() !== 'mysql') {
        return null;
    }

    try {
        $statement = db()->prepare(
            'SELECT id, name, email, password_hash
             FROM admins
             WHERE email = :email AND deleted_at IS NULL
             LIMIT 1'
        );
        $statement->execute(['email' => strtolower(trim($email))]);
        $record = $statement->fetch();

        return is_array($record) ? $record : null;
    } catch (Throwable $exception) {
        error_log('MySQL admin lookup failed: ' . $exception->getMessage());
        return null;
    }
}

function admin_record_from_mysql_by_id(int $id): ?array
{
    if (content_storage_driver() !== 'mysql' || $id <= 0) {
        return null;
    }

    try {
        $statement = db()->prepare(
            'SELECT id, name, email, password_hash
             FROM admins
             WHERE id = :id AND deleted_at IS NULL
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $record = $statement->fetch();

        return is_array($record) ? $record : null;
    } catch (Throwable $exception) {
        error_log('MySQL admin lookup failed: ' . $exception->getMessage());
        return null;
    }
}

function active_admin_credentials(): array
{
    $admin = current_admin();
    $adminId = (int) ($admin['id'] ?? 0);
    $record = admin_record_from_mysql_by_id($adminId);
    if ($record !== null) {
        return [
            'email' => strtolower((string) $record['email']),
            'password_hash' => (string) $record['password_hash'],
            'name' => (string) ($record['name'] ?? 'Banza Admin'),
            'source' => 'mysql',
        ];
    }

    return admin_credentials();
}

function admin_runtime_config_path(): string
{
    return __DIR__ . '/config.php';
}

function write_admin_credentials(string $email, string $passwordHash): bool
{
    $admin = current_admin();
    $adminId = (int) ($admin['id'] ?? 0);
    if (($admin['source'] ?? '') === 'mysql' && content_storage_driver() === 'mysql' && $adminId > 0) {
        try {
            $statement = db()->prepare(
                'UPDATE admins
                 SET email = :email, password_hash = :password_hash, deleted_at = NULL
                 WHERE id = :id'
            );

            return $statement->execute([
                'id' => $adminId,
                'email' => strtolower(trim($email)),
                'password_hash' => $passwordHash,
            ]);
        } catch (Throwable $exception) {
            error_log('MySQL admin credential update failed: ' . $exception->getMessage());
            return false;
        }
    }

    $config = db_config();
    $config['admin'] = is_array($config['admin'] ?? null) ? $config['admin'] : [];
    $config['admin']['email'] = strtolower(trim($email));
    $config['admin']['password_hash'] = $passwordHash;

    $path = admin_runtime_config_path();
    $export = "<?php\nreturn " . var_export($config, true) . ";\n";

    return file_put_contents($path, $export, LOCK_EX) !== false;
}

function is_admin_logged_in(): bool
{
    ensure_session();
    return isset($_SESSION['admin']) && is_array($_SESSION['admin']);
}

function current_admin(): ?array
{
    ensure_session();
    return is_admin_logged_in() ? $_SESSION['admin'] : null;
}

function attempt_admin_login(string $email, string $password): bool
{
    ensure_session();
    $normalizedEmail = strtolower(trim($email));

    $record = admin_record_from_mysql_by_email($normalizedEmail);
    if ($record !== null) {
        if (!password_verify($password, (string) $record['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin'] = [
            'id' => (int) $record['id'],
            'email' => strtolower((string) $record['email']),
            'name' => (string) ($record['name'] ?? 'Banza Admin'),
            'source' => 'mysql',
            'login_at' => date(DATE_ATOM),
        ];
        return true;
    }

    $credentials = admin_credentials();
    $expectedEmail = strtolower(trim((string) ($credentials['email'] ?? '')));
    $hash = (string) ($credentials['password_hash'] ?? '');

    if ($expectedEmail === '' || $hash === '') {
        return false;
    }

    if ($normalizedEmail !== $expectedEmail) {
        return false;
    }

    if (!password_verify($password, $hash)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin'] = [
        'email' => $expectedEmail,
        'name' => 'Banza Admin',
        'source' => 'config',
        'login_at' => date(DATE_ATOM),
    ];

    return true;
}

function logout_admin(): void
{
    ensure_session();
    unset($_SESSION['admin']);
    session_regenerate_id(true);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php');
        $adminPath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        redirect(($adminPath !== '' ? $adminPath : '/admin') . '/login.php');
    }
}

function admin_flash(?string $message = null, string $type = 'success'): ?array
{
    ensure_session();

    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}


function update_current_admin_session(string $email): void
{
    ensure_session();

    if (!isset($_SESSION['admin']) || !is_array($_SESSION['admin'])) {
        return;
    }

    $_SESSION['admin']['email'] = strtolower(trim($email));
}
