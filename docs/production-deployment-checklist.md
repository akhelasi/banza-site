# Production Deployment Checklist

Use this checklist before deploying the Banza site to a real PHP/MySQL host.

## 1. Hosting Requirements

- PHP host with MySQL/MariaDB support.
- PHP extensions: `pdo_mysql`, `fileinfo`, `mbstring`, `json`, `openssl`.
- HTTPS enabled before public launch.
- Writable runtime folders:
  - `SITE/storage/`
  - `SITE/uploads/`

GitHub Pages is not suitable because the project uses PHP and server-side storage.

## 2. Code Deploy

Clone or upload the repository source to the host.

```bash
git clone https://github.com/akhelasi/banza-site.git
```

Recommended web root:

```text
SITE/
```

If the host requires a public folder name such as `public_html`, copy or symlink the contents of `SITE/` there and keep private config outside public download access where the host allows it.

## 3. Production Config

Create an untracked production config:

```bash
cp SITE/includes/config.example.php SITE/includes/config.php
```

Set:

- database host, name, user and password
- real admin email
- generated admin password hash
- `session.secure => true` after HTTPS is enabled
- `content_storage.driver => mysql` only after schema/import/runtime checks are complete

Generate the admin password hash locally or on the host:

```bash
php SITE/scripts/generate-password-hash.php "new-strong-password"
```

Do not commit `SITE/includes/config.php`.

## 4. Database Setup

For a new database:

```bash
mysql -u USER -p DATABASE_NAME < SITE/database/schema.sql
```

For an existing database created before import source keys were added:

```bash
mysql -u USER -p DATABASE_NAME < SITE/database/migrations/2026_06_24_add_import_source_keys.sql
```

Dry-run the JSON import first:

```bash
php SITE/scripts/import-json-to-mysql.php --dry-run --only=all
```

Dry-run the production setup script:

```bash
export BANZA_ADMIN_PASSWORD="replace-with-a-strong-password"
php SITE/scripts/setup-production.php --email=admin@example.com --password-env=BANZA_ADMIN_PASSWORD --dry-run
```

After backup and review, run the import:

```bash
php SITE/scripts/import-json-to-mysql.php --only=all
```

After confirming the setup dry run, run the same setup command without `--dry-run`. This creates or updates the first `admins` row and seeds default settings/social/donation rows from `SITE/storage/content.json`. Use `--force` only when intentionally replacing an existing admin password/name.

Important: runtime MySQL repositories are currently wired only for contact messages. Keep `content_storage.driver=json` until posts/pages/settings runtime repositories are completed and tested, or switch only for the verified contact-message path.

## 5. Uploads And Permissions

Create and make writable:

```text
SITE/uploads/
SITE/storage/
```

Use the narrowest host-supported permissions that allow PHP to write uploaded images and cache files. Avoid world-writable permissions if the host provides a safer owner/group setup.

Runtime files to back up:

- `SITE/uploads/`
- `SITE/storage/content.json` while JSON storage is active
- `SITE/storage/weather-cache.json` is disposable cache and does not need backup

## 6. Backup Plan

Before launch:

- Export the MySQL database.
- Copy `SITE/uploads/`.
- Copy `SITE/storage/content.json` if JSON remains active.
- Save a copy of production `SITE/includes/config.php` outside Git.

Suggested backup rhythm after launch:

- Database: daily or host-managed automatic backup.
- Uploads: daily or weekly depending on content frequency.
- Config: after every credential or host change.

## 7. Rollback Plan

Before every deploy:

1. Note the current Git commit hash.
2. Export the database.
3. Back up `SITE/uploads/`.
4. Deploy the new commit.
5. Run smoke checks.

Rollback:

```bash
git checkout PREVIOUS_GOOD_COMMIT
```

Then restore the database/uploads only if the failed deploy changed data in a non-compatible way.

## 8. Launch Smoke Checks

Public:

- `/index.php`
- `/news.php`
- `/projects.php`
- `/about.php`
- `/history.php`
- `/contact.php`
- `/football.php`

Admin:

- `/admin`
- login with production admin
- create/edit one test news item
- upload one small image
- submit one contact message
- mark the message read
- soft delete and restore a test item

Security:

- HTTPS works.
- Demo admin password no longer works.
- `SITE/includes/config.php` is not public/downloadable.
- Uploads reject non-image files.
- Contact form validates required fields.

## 9. Launch Blockers

Do not launch publicly until these are resolved:

- real admin credentials configured
- real donation account details approved
- real contact/social links approved
- client-approved Georgian content added
- database/uploads backup tested
- mobile visual QA completed in a real browser
- final security review completed after storage/deployment decisions
