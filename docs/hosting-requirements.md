# Hosting Requirements

Use this document when choosing a production host for the Banza site. The project is a PHP/MySQL site, so static hosting and GitHub Pages are not enough.

## Required

- PHP 8.1 or newer.
- MySQL 5.7+ or MariaDB 10.4+.
- PHP extensions:
  - `pdo_mysql`
  - `fileinfo`
  - `mbstring`
  - `json`
  - `openssl`
  - `gd` recommended for image optimization.
- HTTPS with a valid certificate.
- Ability to set the web document root to the `SITE/` directory, or safely publish the contents of `SITE/`.
- Writable runtime directories:
  - `SITE/uploads/`
  - `SITE/storage/`
- Ability to create an untracked production config file:
  - `SITE/includes/config.php`
- Ability to run PHP CLI commands, at least during setup and launch verification.
- Database backup/export access.
- File backup access for `SITE/uploads/`.

## Strongly Recommended

- SSH access or a host control panel terminal.
- Git deploy support, so updates can be pulled from `akhelasi/banza-site`.
- Host-managed automatic MySQL backups.
- Easy file-manager or SFTP access for uploads backup/restore.
- Ability to configure PHP error logging without displaying raw errors to visitors.
- Ability to keep non-public files outside direct browser download access, where the host supports it.

## Questions For The Host

Ask these before buying or configuring hosting:

1. Which PHP version is available, and can it be changed per site?
2. Are `pdo_mysql`, `fileinfo`, `mbstring`, `json`, `openssl` and `gd` enabled?
3. Can the site use MySQL or MariaDB, and how many databases are included?
4. Can PHP write to `uploads/` and `storage/`?
5. Is PHP CLI available from SSH or terminal?
6. Can the domain document root point directly to the `SITE/` folder?
7. How are MySQL and uploaded files backed up?
8. Is HTTPS included and automatically renewed?
9. Can PHP errors be logged privately instead of displayed publicly?
10. Which deploy method should be used: Git, SFTP, file manager, FTP, or SSH?

## Minimum Launch Setup

Before launch, the selected host must support this sequence:

```bash
mysql -u USER -p DATABASE_NAME < SITE/database/schema.sql
php SITE/scripts/setup-production.php --migrate --dry-run
php SITE/scripts/setup-production.php --migrate
php SITE/scripts/import-json-to-mysql.php --dry-run --only=all
php SITE/scripts/import-json-to-mysql.php --only=all
php SITE/scripts/check-mysql-smoke.php --admin-email=REAL_ADMIN_EMAIL --strict
php SITE/scripts/check-launch-readiness.php --strict
php SITE/scripts/setup-production.php --audit-content
php SITE/scripts/setup-production.php --check-routes
```

If the host cannot run PHP CLI commands, use a local/dev machine with the same production database credentials only if that is safe and approved. Do not paste credentials into Codex chat.

## Configuration Notes

- Keep `SITE/includes/config.php` untracked.
- Set `content_storage.driver` to `mysql` only after schema, migrations, import and smoke checks pass.
- Set `session.secure` to `true` after HTTPS is enabled.
- Replace demo admin credentials before public access.
- Back up the database and `SITE/uploads/` before every launch deploy.

## Not Suitable

- GitHub Pages.
- Static-only hosting.
- Hosting without MySQL/MariaDB.
- Hosting that blocks PHP file uploads or write access to runtime folders.
- Hosting that cannot keep real credentials out of Git.
