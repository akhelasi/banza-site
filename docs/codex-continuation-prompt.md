# Codex Continuation Prompt

Use this prompt when continuing the Banza site in a new Codex session, especially on another computer.

```text
Work in this project using AGENTS.md.

Project:
https://github.com/akhelasi/banza-site

First read:
- AGENTS.md
- README.md
- docs/project-checklist.md
- docs/project-worklog.md
- docs/residual-launch-blockers.md
- docs/completion-evidence-matrix.md
- docs/hosting-requirements.md

Current state:
- Public PHP site and admin panel are implemented.
- Local/demo storage uses SITE/storage/content.json.
- MySQL schema, import tooling, runtime readers, write-through sync, admin auth, migrations and smoke helpers exist.
- The project is not production-launch-complete yet.
- Remaining blockers depend on client-approved content/assets/accounts, real hosting/domain, production config, MySQL smoke, production admin credentials and manual browser QA.

Workflow:
1. Inspect the current Git status.
2. Continue from docs/project-checklist.md.
3. After each phase, verify the change, fix any bug found, re-run checks until clean, update docs/project-worklog.md and docs/project-checklist.md, then commit and push to origin/main.
4. Do not commit secrets, SITE/includes/config.php, database dumps, real passwords, private payment/contact data or runtime uploads.
5. Do not mark the full project complete until docs/completion-evidence-matrix.md completion conditions are satisfied.

Useful local checks:

php SITE/scripts/check-local-handoff.php
php SITE/scripts/check-launch-readiness.php
git diff --check
git status --short --ignored

Host-only or normal-terminal checks:

php SITE/scripts/setup-production.php --migrate --dry-run
php SITE/scripts/setup-production.php --migrate
php SITE/scripts/import-json-to-mysql.php --dry-run --only=all
php SITE/scripts/import-json-to-mysql.php --only=all
php SITE/scripts/check-mysql-smoke.php --admin-email=REAL_ADMIN_EMAIL --strict
php SITE/scripts/check-launch-readiness.php --strict
php SITE/scripts/setup-production.php --audit-content
php SITE/scripts/setup-production.php --check-routes

Important:
- Keep production credentials outside Git and outside chat.
- Use docs/hosting-requirements.md before choosing hosting.
- Use docs/client-launch-intake.md for real client content and account details.
- Use docs/manual-qa-checklist.md for desktop/tablet/mobile browser QA.
- Use docs/launch-signoff-template.md outside the repository for final signoff evidence.
```

## First Local Commands

After cloning:

```powershell
git status --short --ignored
php SITE\scripts\check-local-handoff.php
php -S 127.0.0.1:8082 -t SITE
```

Open:

```text
http://127.0.0.1:8082/index.php
http://127.0.0.1:8082/admin
```

Demo admin login:

```text
Email: admin@banza.local
Password: AdminDemo2026!
```

Replace this before public deployment.
