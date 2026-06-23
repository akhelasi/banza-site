# Banza Village Site

PHP website for the village of Banza. The project is being built in phases with Codex.

Repository: `https://github.com/akhelasi/banza-site`

## Current Status

The current codebase includes:

- Public site pages: home, news, news detail, history, projects, about, contact and football.
- Admin panel: content CRUD, media uploads, settings, contact messages and trash.
- Live search/filter/sort on public listing pages.
- Image upload support for news/projects/gallery media.
- Soft delete, restore and permanent delete.
- Contact form with admin inbox.
- `post_date` and `last_update` metadata for admin-managed content.
- Development storage through `SITE/storage/content.json`.
- SQL schema draft in `SITE/database/schema.sql`.

See:

- `docs/project-worklog.md` for completed phases and verification notes.
- `docs/project-checklist.md` for remaining work through production.
- `docs/banza-site-prompts.md` for the original full project prompt.
- `docs/storage-decision.md` for the JSON vs MySQL storage decision.

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

Contact message import dry-run:

```powershell
php SITE\scripts\import-json-to-mysql.php --dry-run --only=contact_messages
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
Phase 16: Continue production hardening.
Either expand MySQL runtime repositories beyond contact messages, or start production config/admin credential hardening if hosting details are known.
```

## Production Before-Launch Checklist

Must be completed before public launch:

- Replace demo admin credentials.
- Replace demo content with client-approved Georgian text.
- Replace demo contact/social/bank details with real values.
- Decide and implement production storage, preferably MySQL.
- Configure production PHP settings and database credentials outside Git.
- Configure upload folder permissions.
- Add backup plan for database and `SITE/uploads/`.
- Complete manual responsive browser QA.
- Complete final security review.
- Add real weather and camera integrations if required for launch.

## Deployment Note

GitHub Pages cannot run this project because it uses PHP and server-side behavior. Use a PHP-capable host with MySQL support for production.
