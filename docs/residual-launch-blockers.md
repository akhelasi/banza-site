# Residual Launch Blockers

Updated: 2026-06-26

This project is code-complete enough for local/demo handoff, but it is not production-launch-complete. The remaining items require client approval, production hosting, real credentials or real browser QA.

## Current Code State

Completed:

- Public pages and admin panel exist.
- Admin can manage launch-critical content surfaces.
- JSON development storage exists.
- MySQL schema, migrations, import tooling, runtime readers, admin auth path and smoke helpers exist.
- Upload validation, trash, contact inbox, rate limiting, honeypot, CSRF and security headers exist.
- Launch readiness, content audit, route smoke and MySQL smoke scripts exist.
- Handoff docs, decision log and client intake checklist exist.

Still not production verified:

- Real host/dev MySQL connection.
- Manual browser QA in a real browser.
- Production admin credentials.
- Real client-approved content, accounts, links and photos.

## Client Content Blockers

Source: `docs/client-launch-intake.md`

Required:

- Client-approved Georgian copy for home, about, history, football, projects and contact pages.
- Real news and project records.
- Final population, household, vineyard and elevation data.
- Approved local photos/assets or explicit approval to keep temporary images.
- Real donation account numbers and account holder details.
- Real social network URLs.
- Real contact email/phone/address notes.

Validation commands after content replacement:

```bash
php SITE/scripts/check-launch-readiness.php --strict
php SITE/scripts/setup-production.php --audit-content
```

Strict readiness also checks that `content_storage.driver=mysql`; keep the local/demo `json` driver only before production.

## Hosting Blockers

Required:

- PHP/MySQL hosting provider.
- Production domain or subdomain.
- Document root path and deploy method.
- MySQL database credentials stored only in untracked `SITE/includes/config.php` on the host.
- Upload/storage write permissions.
- Backup method for MySQL and `SITE/uploads/`.

Use `docs/hosting-requirements.md` to confirm the host supports the required PHP version, extensions, MySQL access, PHP CLI checks, writable runtime folders, HTTPS and backups.

Validation commands on host:

```bash
php SITE/scripts/setup-production.php --migrate --dry-run
php SITE/scripts/setup-production.php --migrate
php SITE/scripts/import-json-to-mysql.php --dry-run --only=all
php SITE/scripts/import-json-to-mysql.php --only=all
php SITE/scripts/check-mysql-smoke.php --admin-email=admin@example.com --strict
```

## Credential Blockers

Required:

- Real production admin email.
- Strong production admin password.
- Client-approved owner of the admin account.
- SMTP/provider credentials only if email notifications are required.

Do not commit:

- `SITE/includes/config.php`
- passwords
- database dumps
- SMTP credentials
- API keys

## Manual QA Blockers

Automated browser QA remains blocked inside this Codex desktop environment by local permission errors. Complete QA outside this sandbox using `docs/manual-qa-checklist.md`.

Required manual checks:

- Desktop responsive layout.
- Tablet responsive layout.
- Mobile responsive layout.
- Header navigation.
- Search/filter/sort controls.
- Modals for camera, weather, donation, images and YouTube videos.
- Contact form.
- Admin login/profile.
- Admin create/edit/delete/trash flows.
- Upload one small test image on the host.
- Keyboard navigation.
- Screen reader labels/headings.
- Georgian text overflow on small screens.

## Integration Blockers

Camera:

- Real stream/embed URL is waiting for camera purchase/installation.

Weather:

- Current foundation supports server-side cached weather settings.
- Client must approve provider/API approach and exact coordinates/nearby places.

Email:

- Contact inbox works without SMTP.
- Optional email notification requires host/client SMTP/provider details.

## Known Residual Risk

- JSON remains the local editing source with MySQL sync. This is acceptable for handoff/demo, but production multi-admin editing should be treated carefully.
- `SITE/scripts/check-launch-readiness.php --strict` reports the JSON driver as a launch blocker until production config switches to MySQL.
- A fully MySQL-native admin CRUD rewrite should wait until the host, editor count and production workflow are known.

## Recommended Next Human Step

1. Fill out `docs/client-launch-intake.md` with the client.
2. Choose PHP/MySQL hosting and domain.
3. Create untracked production config on the host.
4. Run import/setup/smoke commands on the host.
5. Replace demo content through admin or JSON/import workflow.
6. Complete manual browser QA.
7. Run strict launch checks.
