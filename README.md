# Banza Village Site

PHP website for the village of Banza. The project is being built in phases with Codex.

Repository: `https://github.com/akhelasi/banza-site`

## Current Status

The current codebase includes:

- Public site pages: home, news, news detail, project detail, history, projects, about, contact and football.
- Admin panel: content CRUD, media uploads, settings, contact messages and trash.
- Admin table search/filter/sort for content, messages and trash.
- Admin media library search/filter/sort.
- Admin profile page for changing the runtime admin email/password in untracked config.
- Bulk actions for admin content and contact message lists.
- Admin-editable image alt text for news, projects and football content.
- Live search/filter/sort on public listing pages.
- Load-more pagination on news and project listings.
- Homepage empty states for news/projects when admin content is empty.
- Image upload support for news/projects/gallery media.
- Upload validation includes file type, 5MB size limit, 6000x6000px max dimensions and optional GD-based image optimization.
- Soft delete, restore and permanent delete.
- Contact form with admin inbox.
- Admin login and contact form rate limiting.
- `post_date` and `last_update` metadata for admin-managed content.
- Development storage through `SITE/storage/content.json`.
- SQL schema draft in `SITE/database/schema.sql`.
- Production config hardening: untracked config file, session cookie settings, security headers and password hash helper.
- Weather integration foundation: Open-Meteo live fetch with server-side cache and admin fallback; camera stream URL support.

See:

- `docs/project-worklog.md` for completed phases and verification notes.
- `docs/project-checklist.md` for remaining work through production.
- `docs/banza-site-prompts.md` for the original full project prompt.
- `docs/storage-decision.md` for the JSON vs MySQL storage decision.
- `docs/production-deployment-checklist.md` for production hosting/deploy steps.

## Project Layout

```text
SITE/
  admin/              Admin panel pages
  assets/             CSS, JS and committed image assets
  database/schema.sql Draft MySQL schema
  includes/           Shared PHP helpers, auth, layout and storage logic
  storage/content.json Development content storage
  uploads/            Runtime upload folder; uploaded files are not committed

AGENTS.md            Codex instructions for this project
.agents/             Local Codex skills
docs/                Worklog, checklist and setup notes
templates/           Starter-pack templates
scripts/             Starter-pack scripts
```

## Local Development

From the repository root:

```powershell
php -S 127.0.0.1:8082 -t SITE
```

Open:

```text
http://127.0.0.1:8082/index.php
```

Admin panel:

```text
http://127.0.0.1:8082/admin
```

XAMPP-style URL may also be:

```text
http://localhost/SITE/
```

## Demo Admin Login

Development login:

```text
Email: admin@banza.local
Password: AdminDemo2026!
```

Important: replace this before any public deployment.

The demo password hash is stored in `SITE/includes/config.example.php`. Real production credentials must be kept outside Git, for example in an untracked `SITE/includes/config.php` or environment variables.


## Production Config

For production, do not edit or commit `SITE/includes/config.example.php` with real secrets.

1. Copy `SITE/includes/config.example.php` to `SITE/includes/config.php` on the server.
2. Generate a new admin password hash:

```powershell
php SITE\scripts\generate-password-hash.php "your-new-strong-password"
```

3. Put the generated value into `admin.password_hash` in `SITE/includes/config.php`.
4. Replace the demo admin email with the real admin email.
5. Set `session.secure` to `true` after the site is served through HTTPS.
6. Keep `SITE/includes/config.php` untracked; `.gitignore` excludes it.

The site sends these PHP security headers by default: `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `Referrer-Policy: strict-origin-when-cross-origin`.
## Storage Model

Current development storage:

```text
SITE/storage/content.json
```

This is useful for fast local development and Codex handoff, but it is not the preferred production storage for multi-admin editing.

Production recommendation:

1. Use MySQL.
2. Implement repositories for posts, pages, settings, media and contact messages.
3. Add an import script from `SITE/storage/content.json`.
4. Keep uploaded media under `SITE/uploads/` and back it up separately.

The current decision is documented in `docs/storage-decision.md`: keep JSON for development/content approval, then move to MySQL before public launch.

## Runtime Uploads

Committed:

```text
SITE/uploads/.gitkeep
```

Ignored:

```text
SITE/uploads/*
```

Uploaded images are runtime data. They must be copied/backed up separately when moving hosting environments.

## Verification Commands

PHP syntax for all PHP files:

```powershell
Get-ChildItem SITE -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

JavaScript syntax:

```powershell
node --check SITE\assets\js\main.js
```

JSON content storage:

```powershell
Get-Content SITE\storage\content.json -Raw | ConvertFrom-Json | Out-Null
```

JSON-to-MySQL import dry-run:

```powershell
php SITE\scripts\import-json-to-mysql.php --dry-run --only=all
```

Supported `--only` targets: `all`, `pages`, `posts`, `settings`, `social_links`, `donation_accounts`, `media_items`, `contact_messages`.

Production setup dry-run:

```powershell
php SITE\scripts\setup-production.php --email=admin@example.com --password-env=BANZA_ADMIN_PASSWORD --dry-run
```

After `schema.sql` has been loaded into MySQL, run the same command without `--dry-run` to create or update the first admin row and seed default settings/social/donation rows. Use `--force` only when intentionally replacing an existing admin row with the same email.

For an existing MySQL database created before the import source keys were added, run:

```powershell
mysql -u root -p banza_site < SITE\database\migrations\2026_06_24_add_import_source_keys.sql
mysql -u root -p banza_site < SITE\database\migrations\2026_06_24_add_media_caption.sql
```

Git whitespace check:

```powershell
git diff --check
```

Expected note on Windows: Git may warn that LF will be replaced by CRLF. That is not a functional failure.

## Git Workflow

After each completed phase:

```powershell
git status --short
git add <changed-files>
git commit -m "Short phase summary"
git push origin main
```

Do not commit:

- real secrets or tokens
- production config
- database dumps
- runtime uploaded files
- temporary QA files

## Continue With Codex

When continuing in a new Codex session, start with:

```text
Read AGENTS.md, docs/project-worklog.md and docs/project-checklist.md.
Continue from the next unchecked phase in docs/project-checklist.md.
After each phase, run the relevant checks, update docs/project-worklog.md and docs/project-checklist.md, commit, and push to origin/main.
```

Recommended next phase:

```text
Next: complete remaining production blockers, especially client-approved content, real donation/contact/social values, and final real-browser visual QA.
```

## Production Before-Launch Checklist

Must be completed before public launch:

- Replace demo admin credentials using SITE/scripts/generate-password-hash.php.
- Replace demo content with client-approved Georgian text.
- Replace demo contact/social/bank details with real values.
- Decide and implement production storage, preferably MySQL.
- Configure production PHP settings and database credentials outside Git.
- Configure upload folder permissions.
- Add backup plan for database and `SITE/uploads/`.
- Complete manual responsive browser QA.
- Complete final security review.
- Add the real camera stream URL after the client buys/installs the camera.

## Deployment Note

GitHub Pages cannot run this project because it uses PHP and server-side behavior. Use a PHP-capable host with MySQL support for production.
