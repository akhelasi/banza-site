# Banza Site Worklog

This file records what Codex changed during the Banza village site build so the work can continue from another machine or Codex thread.

## Repository

- GitHub: https://github.com/akhelasi/banza-site
- Main site source: `SITE/`
- Starter pack/instructions: `AGENTS.md`, `.agents/`, `docs/`, `templates/`, `scripts/`
- Project type: PHP/SQL-oriented site, source stored on GitHub. GitHub Pages is not used because PHP will not run there.

## Operating Rule Going Forward

After each completed phase:

1. Run the smallest meaningful checks for the changed files.
2. Fix discovered bugs.
3. Re-run checks until the phase has no known blocking issues.
4. Update this worklog.
5. Commit and push to GitHub.

## Saved Prompts

- `docs/banza-site-prompts.md` stores the full project prompt and phase 0-2 prompt.

## Phase 0: Research And Seed Content

Added seed/demo content for Banza so the site is not empty during UI and admin testing.

Added:

- `SITE/content-sources.md`
- Seed content in `SITE/includes/data.php`

Sources used:

- Georgian Wikipedia: https://ka.wikipedia.org/wiki/ბანძა
- English Wikipedia: https://en.wikipedia.org/wiki/Bandza
- Geostat: https://www.geostat.ge/
- Wikimedia Commons image redirect: https://commons.wikimedia.org/wiki/Special:Redirect/file/St.%20Virgin%20church%20of%20Bandza%20Angle.jpg

Notes:

- Some numbers are demo placeholders and must be checked with the client.
- Donation accounts, social links, camera, weather and project/news records are demo until client provides official data.

## Phase 1: Foundation

Added/reworked:

- `.gitignore`: added exception so `SITE/database/schema.sql` can be tracked.
- `SITE/includes/helpers.php`: escaping, assets, CSRF, redirects, helpers.
- `SITE/includes/database.php`: PDO connection scaffold.
- `SITE/includes/config.example.php`: DB config plus demo admin credential hash.
- `SITE/database/schema.sql`: starter schema for admins, pages, posts, media, settings, social links and donation accounts.
- `SITE/assets/images/`: copied user-provided project images.

Images added:

- `SITE/assets/images/football-team.png`
- `SITE/assets/images/donation-fund.png`
- `SITE/assets/images/banza-logo-source.png`
- `SITE/assets/images/banza-logo.svg`

Verification:

- PHP syntax checks passed for changed PHP files.

## Phase 2: Public Homepage

Rebuilt homepage into a real Banza village landing page.

Changed:

- `SITE/index.php`
- `SITE/assets/css/style.css`
- `SITE/assets/js/main.js`

Implemented:

- Header with Banza logo, nav, social links, donation button.
- Hero section with village image, camera card and weather card.
- Camera modal, weather modal, donation modal.
- Sidebar with donation, popular projects and follow links.
- Main content with football team feature, latest news, about preview and history preview.
- Responsive layout and mobile overflow fixes.

Verification:

- PHP syntax checks passed.
- JS syntax check passed.
- Local PHP server returned 200.
- Headless screenshot QA was attempted; Chrome/Edge was inconsistent in this Windows environment, but desktop screenshot was inspected and mobile overflow issue was fixed.

## Logo Update

User provided a Banza logo image for the header.

Added:

- `SITE/assets/images/banza-logo-source.png`
- `SITE/assets/images/banza-logo.svg`

Changed:

- Header now uses the SVG logo for sharp rendering.
- `SITE/assets/css/style.css` updated brand/logo sizing.

Verification:

- `php -l SITE/index.php` passed.
- SVG XML parse passed.
- Homepage and logo asset returned 200 locally.

## Phase 3: Public Pages

Added public navigation pages.

Added:

- `SITE/news.php`
- `SITE/news-detail.php`
- `SITE/history.php`
- `SITE/projects.php`
- `SITE/about.php`
- `SITE/contact.php`
- `SITE/football.php`
- `SITE/includes/layout.php`

Implemented:

- Shared public header/footer/page hero layout.
- News listing and news detail page.
- Gallery lightbox hooks.
- YouTube video modal hooks.
- History, projects, about, contact and football pages.
- Live search/filter with no page reload.

Verification:

- PHP syntax checks passed for all new pages.
- JS syntax check passed.
- Local route checks returned 200 for all public pages.
- Missing news slug returned 404.
- Rendered HTML checks confirmed filter and media hooks.

## Phase 3.1: Sort For Filters

User requested sort controls for search/filter pages.

Changed:

- `SITE/news.php`
- `SITE/projects.php`
- `SITE/history.php`
- `SITE/about.php`
- `SITE/contact.php`
- `SITE/assets/js/main.js`
- `SITE/assets/css/style.css`

Implemented:

- Sort by date asc/desc and title asc/desc on news.
- Sort by title/status on projects.
- Sort by title on about/history/contact filtered content.
- Sorting happens live without page reload or scroll reset.

Bug found and fixed:

- A first replacement inserted literal `` `r`n `` text into HTML. Affected pages were rewritten cleanly and rechecked.

Verification:

- PHP syntax checks passed.
- JS syntax check passed.
- Rendered HTML checks confirmed `name="sort"`, `data-sort-*`, and no literal newline tokens.

## Phase 4: Admin Panel Skeleton

Added protected admin area.

Added:

- `SITE/includes/auth.php`
- `SITE/includes/admin-layout.php`
- `SITE/admin/login.php`
- `SITE/admin/logout.php`
- `SITE/admin/index.php`
- `SITE/admin/content.php`
- `SITE/admin/media.php`
- `SITE/admin/settings.php`
- `SITE/admin/trash.php`

Implemented:

- Admin login/logout.
- Session protection.
- CSRF validation on admin forms.
- Admin dashboard.
- Admin navigation shell.
- Placeholder admin pages for content, media, settings and trash.

Demo login:

- Email: `admin@banza.local`
- Password: `AdminDemo2026!`

Security note:

- Demo credentials are for local scaffold only. Replace `SITE/includes/config.php` or config values before production.

Verification:

- PHP syntax checks passed.
- Protected dashboard redirects to login without session.
- Correct login opens dashboard.
- Wrong password is rejected.
- Logout returns user to login.
- Public routes still returned 200 after admin changes.

## Phase 5: Content CRUD With Dev Storage

Added a file-backed storage/repository layer so admin changes appear on public pages before MySQL CRUD is wired.

Added:

- `SITE/includes/content-store.php`
- `SITE/storage/.gitkeep`
- `SITE/storage/content.json`

Changed:

- `SITE/includes/data.php`: reads from storage when available.
- `SITE/admin/content.php`: real CRUD for news/projects and static page edit forms.
- `SITE/assets/css/style.css`: admin form and action styles.

Implemented:

- News create/edit/soft-delete.
- Projects create/edit/soft-delete.
- Static page editing for about, history, football and contact.
- Public pages reflect admin changes from `SITE/storage/content.json`.
- Storage fallback still uses seed content if JSON is absent/invalid.

Bug found and fixed:

- `helpers.php` was damaged by a bad regex replacement while updating asset paths. The file was rewritten cleanly and `php -l` passed.
- Temporary QA CRUD record was removed from storage after testing.

Verification:

- PHP syntax checks passed.
- JS syntax check passed.
- JSON storage validation passed.
- CRUD flow tested: create news, see it on public page, edit it, see updated detail, delete it, confirm list hides it and detail returns 404.
- Static page edit/revert flow tested successfully.
- Public smoke routes returned 200.
- Authenticated admin CRUD routes returned 200 and showed admin shell.


## Phase 6: Uploads, Gallery And Videos

Implemented admin-managed image upload support.

Added:

- `SITE/includes/uploads.php`
- `SITE/uploads/.gitkeep`

Changed:

- `.gitignore`: runtime uploads are ignored except `SITE/uploads/.gitkeep`.
- `SITE/admin/media.php`: now supports secure image upload and lists uploaded images.
- `SITE/admin/content.php`: news/project forms support main image upload; news form supports multiple gallery image uploads.
- `SITE/assets/css/style.css`: file input and media path UI styles.

Implemented:

- Upload validation for JPG, PNG, WEBP and GIF.
- Max upload size: 5MB.
- MIME detection with `finfo`.
- Image validation with `getimagesize`.
- Randomized stored filenames under `SITE/uploads/YYYY/MM/`.
- Admin media library displays uploaded file paths for reuse.
- News detail page already renders gallery lightbox and YouTube video modal hooks from stored content.

Runtime upload policy:

- Real uploaded files are runtime content and are ignored by Git.
- Only `SITE/uploads/.gitkeep` is committed so the folder exists after clone.
- If production content must be migrated, copy the `SITE/uploads/` folder separately or use hosting backups.

Bugs/edge cases checked:

- CLI-style fake upload is rejected by `is_uploaded_file`, which is expected and safer.
- Invalid text file upload through admin media is rejected.
- Valid PNG upload through real HTTP multipart succeeds.
- News create flow with main image upload and gallery upload shows uploaded paths on public detail.
- Temporary QA records and uploaded test files were removed after testing.

Verification:

- `php -l SITE/includes/uploads.php` passed.
- `php -l SITE/admin/media.php` passed.
- `php -l SITE/admin/content.php` passed.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` validated as JSON.
- Admin media route returned 200 and showed upload UI.
- Admin news create route returned 200 and showed file inputs.
- Public news detail route returned 200 and showed gallery/lightbox hooks.


## Phase 7: Trash / Soft Delete Restore And Permanent Delete

Implemented restore and permanent delete for soft-deleted content.

Changed:

- `SITE/admin/trash.php`: now lists deleted news/projects and supports restore/permanent delete.
- `SITE/includes/content-store.php`: added upload path reference collection and safe uploaded-file deletion helpers.

Implemented:

- Trash listing for soft-deleted news and projects.
- Restore action returns records to public pages.
- Permanent delete removes records from storage.
- Permanent delete checks uploaded paths and deletes files only when they are under `SITE/uploads/` and no remaining content references them.
- Public pages continue to hide soft-deleted content through `visible_content_items()`.

Bug found and fixed:

- `admin/trash.php` initially required `content-store.php` twice because `data.php` already loads it. This caused a fatal redeclare error. Changed the direct include to `require_once` and re-ran QA.

Verification:

- `php -l SITE/admin/trash.php` passed.
- `php -l SITE/includes/content-store.php` passed.
- `SITE/storage/content.json` validated as JSON.
- End-to-end trash flow tested: create test news, soft-delete it, confirm Trash lists it, restore it, confirm public detail returns 200, soft-delete again, permanent-delete it, confirm detail returns 404 and storage record is gone.
- QA marker scan confirmed no temporary test records remain.


## Phase 8: Admin Settings Management

Implemented admin-managed site settings for reusable public-site configuration.

Changed:

- `SITE/admin/settings.php`: replaced placeholder settings summary with editable forms.
- `SITE/assets/css/style.css`: added fieldset styling for grouped admin settings fields.

Implemented:

- Social links management from admin panel.
- Donation bank accounts management from admin panel.
- Live camera title, status, preview image and description management.
- Weather summary, temperature, wind, humidity, rain chance and nearby-place forecast management.
- CSRF-protected settings save flow.
- Basic validation requiring at least one social link and one donation account.
- Settings persist into `SITE/storage/content.json` through the existing content store.

Verification:

- `php -l SITE/admin/settings.php` passed.
- `node --check SITE/assets/js/main.js` passed.
- End-to-end settings flow tested through local HTTP: admin login, settings POST, homepage reflects updated social/donation/camera/weather values.
- Cleanup verified: temporary `SETTINGS_QA` markers were removed and the content store was restored.
- Unauthenticated settings route returns admin redirect behavior as expected.

Browser QA note:

- Playwright/in-app browser automation was attempted through Node REPL but the local kernel failed with an `EPERM` filesystem permission error. This phase was verified with HTTP smoke tests, syntax checks and CSS/layout review.

## Phase 9: Final QA, Polish And Security Review

Ran a full final QA pass after the admin settings phase.

Verification:

- Full PHP syntax pass across all `SITE/**/*.php` files passed.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` parsed successfully as JSON.
- Required PHP extensions checked for current features: `fileinfo` and `mbstring` are available.
- Public route smoke test passed for home, news, news detail, history, projects, about, contact and football pages.
- Missing news detail slug correctly returns 404.
- Admin routes redirect when unauthenticated.
- Authenticated admin routes return 200 for dashboard, content, media, settings and trash.
- Listing pages contain live search/sort hooks, filter items and empty states.
- Frontend JS contains the live filter/sort event wiring and DOM reorder behavior.

Security/polish review:

- State-changing admin forms use CSRF checks.
- Admin login uses `password_verify`.
- Output escaping is centralized through `e()` / `htmlspecialchars`.
- Upload handling validates MIME/type, size and image shape before `move_uploaded_file`.
- Permanent file deletion is constrained to safe uploaded paths and only deletes unreferenced uploads.

Known QA limitation:

- Browser automation through Node REPL/Playwright remains blocked in this environment by an `EPERM` filesystem permission error, so final responsive/browser visual QA still needs a manual browser pass before production.


## Phase 10: Contact Form And Admin Inbox

Implemented public contact form handling and admin message management.

Added:

- `SITE/admin/messages.php`

Changed:

- `SITE/contact.php`: replaced disabled placeholder form with a working POST form.
- `SITE/includes/content-store.php`: added `contactMessages` to storage defaults.
- `SITE/includes/data.php`: exposes stored contact messages.
- `SITE/includes/admin-layout.php`: added admin navigation link for messages.
- `SITE/admin/index.php`: dashboard now shows unread contact message count.
- `SITE/admin/trash.php`: contact messages now participate in restore/permanent-delete trash flow.
- `SITE/assets/css/style.css`: added contact form grid, honeypot, message status and admin message preview styles.
- `SITE/database/schema.sql`: added future MySQL `contact_messages` table.

Implemented:

- CSRF-protected public contact form.
- Server-side validation for name, email, phone, subject and message.
- Honeypot field for simple spam reduction.
- Contact messages persist into `SITE/storage/content.json`.
- Admin inbox lists submitted messages with sender, email, phone, subject, body and created date.
- Admin can mark messages as read.
- Admin can soft-delete messages into Trash.
- Trash can restore or permanently delete contact messages.

Verification:

- Full PHP syntax pass across all `SITE/**/*.php` files passed.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` parsed successfully as JSON.
- End-to-end contact flow passed through local HTTP before the current tool usage limit was reached: public submit, admin inbox visibility, mark read, soft delete, trash restore, soft delete again, permanent delete and cleanup.
- Invalid contact form validation check passed.
- Route smoke check passed for contact, admin dashboard, admin messages and trash pages.
- `git diff --check` passed with CRLF normalization warnings only.
- QA marker scan confirmed no temporary `QA_CONTACT_PHASE10` records remain in storage.

Known QA limitation:

- Browser automation through Node REPL/Playwright remains blocked in this environment by an `EPERM` filesystem permission error, so responsive visual QA still needs a manual browser pass before production.


## Phase 11: Admin Redirect And Content Date Metadata

Fixed the `/admin` no-trailing-slash login redirect and added content date metadata.

Changed:

- `SITE/includes/auth.php`: admin-only redirects now point to the absolute admin login path, so `http://127.0.0.1:8082/admin` reaches `/admin/login.php` instead of `/login.php`.
- `SITE/includes/content-store.php`: added `content_date_today()` and `touch_content_dates()` helpers.
- `SITE/admin/content.php`: news, projects and static page saves now write `post_date` and update `last_update`; soft delete also refreshes `last_update`.
- `SITE/contact.php`: submitted contact messages now include `post_date` and `last_update`.
- `SITE/admin/messages.php`: mark-read and soft-delete actions refresh `last_update`.
- `SITE/admin/trash.php`: restored records refresh `last_update`.
- `SITE/database/schema.sql`: added `post_date` and `last_update` fields to content-oriented tables and MySQL triggers that fill/update them in `dd/mm/YYYY` format.

Verification:

- `/admin` now resolves to the admin login page instead of a root `/login.php` 404.
- End-to-end admin content create flow verified that new content gets `post_date` and `last_update` in `dd/mm/YYYY` format.
- Temporary QA content was restored/removed after testing.
- Full PHP syntax pass across all `SITE/**/*.php` files passed.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` parsed successfully as JSON.
- `git diff --check` passed with CRLF normalization warnings only.

## Phase 12: README Setup And Handoff Cleanup

Prepared the project for easier continuation in a new Codex session or workplace environment.

Changed:

- `README.md`: replaced the starter placeholder README with a practical project handoff.
- `docs/project-checklist.md`: marked README/setup/handoff items as complete and moved the recommended next phase to Phase 13.

Documented:

- Current project status.
- Local PHP dev server command.
- Admin URL and demo credentials.
- Development JSON storage model.
- Runtime upload policy.
- Verification commands.
- Git phase workflow.
- "Continue With Codex" handoff prompt.
- Production before-launch checklist.
- Why GitHub Pages is not suitable for this PHP project.

Verification:

- README and checklist were reviewed for consistency with the current repository layout and Phase 11 state.
- No product PHP/CSS/JS behavior was changed in this phase.


## Phase 13: Storage Direction Decision

Decided the storage path before starting production backend work.

Added:

- `docs/storage-decision.md`

Changed:

- `README.md`: linked the storage decision and updated the next recommended phase.
- `docs/project-checklist.md`: marked the storage decision as complete and moved Phase 14 to NEXT.

Decision:

- Keep JSON storage only for development, Codex handoff and content/design approval.
- Do not treat JSON storage as the production backend.
- Move to MySQL before public launch.
- Implement the MySQL migration incrementally rather than rewriting all CRUD at once.

Reasoning:

- JSON is fast and practical while content/design/admin behavior is still changing.
- JSON is risky for production because of concurrency, backup, search/pagination and multi-admin editing concerns.
- `SITE/database/schema.sql` already gives a MySQL target model, so the next phase should begin wiring repositories and an import path.

Recommended next implementation:

- Phase 14 should add a repository structure and keep JSON as the default fallback.
- Implement/import one content area first, then expand to the rest after verification.

Verification:

- Documentation was checked against the current `content-store.php`, `schema.sql`, README and checklist.
- No product PHP/CSS/JS behavior was changed in this phase.


## Phase 14: Incremental MySQL Import Slice

Started the MySQL migration in a controlled way while keeping JSON as the default runtime storage.

Added:

- `SITE/includes/repositories/contact-message-repository.php`
- `SITE/scripts/import-json-to-mysql.php`

Changed:

- `SITE/includes/config.example.php`: added `content_storage.driver`, defaulting to `json`.
- `SITE/database/schema.sql`: added a unique `slug` field to `contact_messages` so JSON messages can be imported idempotently.
- `README.md`: documented the contact message import dry-run command and updated the next phase.
- `docs/project-checklist.md`: marked the first MySQL import slice as done and moved MySQL runtime repository expansion to NEXT.

How it works:

- Runtime public/admin behavior still uses JSON, so the site remains easy to clone and run.
- The new contact message repository normalizes JSON contact message records and upserts them to MySQL with prepared statements.
- The import script currently supports `--only=contact_messages`.
- The import script supports `--dry-run`, which validates JSON and counts importable messages without touching MySQL.
- Real import uses a transaction and rolls back on failure.

Problems found and fixed:

- During QA, a temporary PowerShell JSON write used UTF-8 with BOM. PHP `json_decode` rejected that temporary BOM-prefixed file.
- The product code was not changed for this; the QA script was corrected to write UTF-8 without BOM and to check native process exit codes.
- Backup/restore cleanup confirmed no QA marker remained in `SITE/storage/content.json`.

Verification:

- `php -l SITE/includes/repositories/contact-message-repository.php` passed.
- `php -l SITE/scripts/import-json-to-mysql.php` passed.
- `php -l SITE/includes/config.example.php` passed.
- Import dry-run with current content passed.
- Import dry-run with a temporary QA contact message counted exactly one importable message.
- Temporary QA content was restored and JSON validation passed after cleanup.
- Route smoke passed for public home/news/contact and authenticated admin dashboard/messages pages.

Next phase notes:

- Keep JSON as default until a runtime MySQL slice is verified.
- Phase 15 should wire one runtime MySQL repository path behind `content_storage.driver=mysql`, preferably contact messages first because that path is isolated and lower risk than news/pages.
- Do not run real import against production data without explicit approval and a backup.

## Phase 15: Contact Messages MySQL Runtime Slice

Expanded the contact message MySQL work from import-only to a runtime repository path while keeping JSON as the default fallback.

Changed:

- `SITE/includes/database.php`: added `content_storage_driver()`.
- `SITE/includes/repositories/contact-message-repository.php`: added runtime read/write/update/delete helpers for contact messages.
- `SITE/contact.php`: contact form saves to MySQL when `content_storage.driver=mysql`; otherwise it keeps the existing JSON behavior.
- `SITE/admin/messages.php`: admin inbox reads, marks read and soft-deletes through MySQL when the driver is `mysql`; otherwise it keeps JSON behavior.
- `SITE/admin/trash.php`: Trash can list, restore and permanently delete MySQL contact messages when the driver is `mysql`; news/projects remain on the existing JSON flow.
- `README.md`: updated the next phase guidance.
- `docs/project-checklist.md`: marked Phase 15 as complete and moved production hardening to NEXT.

How it works:

- Default config remains `content_storage.driver=json`, so clone-and-run behavior is unchanged.
- Setting `content_storage.driver=mysql` routes contact message runtime operations through PDO repositories.
- MySQL queries use prepared statements.
- JSON fallback continues to support public contact submit, admin inbox, soft delete and Trash restore.

Problems found and fixed:

- A large one-shot PowerShell runtime QA script failed with Windows `Access is denied`.
- The test was split into smaller steps: submit, admin action verification and cleanup. The smaller checks passed and made the failure easier to isolate.
- An inline `php -r` repository normalizer test failed because PowerShell quoting corrupted the PHP code. It was rerun through stdin instead.
- The first normalizer assertion was too strict about timezone conversion. The test was corrected to assert MySQL datetime format plus `dd/mm/YYYY` display date normalization, which is what the code contract actually needs.

Verification:

- Full PHP syntax pass across all `SITE/**/*.php` files passed.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` parsed successfully as JSON.
- Contact message import dry-run passed.
- Repository normalizer unit-style check passed through PHP stdin.
- JSON fallback end-to-end contact flow passed: public submit, admin inbox visibility, soft delete, Trash listing and restore.
- QA content was restored from backup and marker cleanup passed.
- Final route smoke passed for public home/contact/admin login and authenticated admin dashboard/messages/trash.
- `git diff --check` passed with CRLF normalization warnings only.

Next phase notes:

- Contact messages now prove the runtime repository switch pattern.
- Phase 16 should harden production config/admin credentials, unless hosting/database details are ready and the next priority is expanding MySQL repositories to posts/pages/settings.
- Do not switch `content_storage.driver` to `mysql` in production until `schema.sql` has been applied and contact message import/runtime behavior has been tested against the target database.



## Phase 16: Production Config And Security Hardening

Hardened the production configuration and session/security defaults without changing the default JSON development runtime.

Changed:

- `.gitignore`: excludes `SITE/includes/config.php` and `SITE/includes/config.local.php` so real production credentials stay outside Git.
- `SITE/includes/config.example.php`: added configurable `session` and `security` sections.
- `SITE/includes/helpers.php`: added shared app session startup and basic security header helpers.
- `SITE/includes/auth.php`: admin sessions now use the shared session startup helper.
- `SITE/includes/layout.php` and `SITE/includes/admin-layout.php`: public/admin responses send basic security headers before output.
- `SITE/admin/login.php`: the standalone login page sends the same security headers before output.
- `SITE/scripts/generate-password-hash.php`: added a CLI helper for generating production admin password hashes.
- `README.md`: documented production config setup and password hash workflow.
- `docs/project-checklist.md`: marked Phase 16 hardening items complete and moved Phase 17 to NEXT.

How it works:

- Development still uses the demo config/example values unless a local `SITE/includes/config.php` exists.
- Production should copy `config.example.php` to untracked `config.php`, replace the admin email/password hash and set `session.secure=true` after HTTPS is enabled.
- Session cookies now have a project-specific name, HttpOnly, SameSite=Lax and a configurable Secure flag.
- PHP responses now send `X-Frame-Options`, `X-Content-Type-Options` and `Referrer-Policy` headers by default.

Problems found and fixed:

- `apply_patch` partially applied the first edit, then failed under the Windows sandbox ACL helper. The affected files were inspected, duplicate `.gitignore` entries were removed and the remaining edits were applied with targeted PowerShell file writes.
- The first HTTP smoke script used `$home`, which conflicts with PowerShell's read-only `$HOME` variable. The script was corrected to use `$homeResponse` and the header check passed.
- A follow-up HTTP smoke test found that `SITE/admin/login.php` did not use the shared admin layout and therefore missed the new security headers. Added `send_security_headers()` before login page output and reran the checks.

Verification:

- Full PHP syntax pass across all `SITE/**/*.php` files passed.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` parsed successfully as JSON.
- Contact message import dry-run passed.
- Password hash generator produced a valid password hash and usage note.
- Local HTTP smoke confirmed homepage/contact/admin login return 200 and include security headers.
- Admin `/admin` redirect still points to `/admin/login.php`.
- Demo admin login still works and uses the `banza_admin_session` cookie.
- `git diff --check` passed with CRLF normalization warnings only.

Next phase notes:

- Phase 17 should replace demo content/assets/links/accounts with client-approved Georgian content, unless hosting/database details are ready and MySQL repository expansion is higher priority.
- Real production admin credentials still need to be chosen by the client and placed only in untracked production config.

## Phase 17: Expanded MySQL Import Coverage

Client-approved replacement content is still waiting on real client values, so this phase continued the production-critical MySQL migration path instead.

Added:

- `SITE/includes/repositories/content-import-repository.php`
- `SITE/database/migrations/2026_06_24_add_import_source_keys.sql`

Changed:

- `SITE/scripts/import-json-to-mysql.php`: supports `all`, `pages`, `posts`, `settings`, `social_links`, `donation_accounts` and `contact_messages` import targets.
- `SITE/database/schema.sql`: added nullable unique `source_key` columns for imported media, social links and donation accounts.
- `.gitignore`: allows SQL migration files under `SITE/database/migrations/`.
- `README.md`: documented the expanded import dry-run and source-key migration command.
- `docs/project-checklist.md`: marked expanded import coverage complete and moved real weather/camera integrations to NEXT while client-approved content remains WAITING.

How it works:

- Dry-run mode validates the JSON storage and reports importable counts without opening a database connection.
- Real import uses one transaction and prepared statements.
- Pages are imported as structured JSON payloads in `pages.body` so static page-specific fields can be recovered by a future runtime repository.
- News and projects import into `posts`; gallery images and YouTube videos import into `media` with stable source keys.
- Camera/weather import into `settings`; social links and donation accounts import into their dedicated tables.
- Contact message import keeps the existing repository path.

Verification:

- Full PHP syntax pass across all `SITE/**/*.php` files passed.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` parsed successfully as JSON.
- `php -l SITE/includes/repositories/content-import-repository.php` passed.
- `php -l SITE/scripts/import-json-to-mysql.php` passed.
- `php SITE/scripts/import-json-to-mysql.php --dry-run --only=all` passed and reported pages, posts, media, settings, social links, donation accounts and contact messages.
- Individual dry-runs for `posts`, `pages`, `social_links` and `contact_messages` passed.
- A normalizer assertion script confirmed expected counts and source-key shapes.
- `git diff --check` passed with CRLF normalization warnings only.

Next phase notes:

- Real MySQL import was not executed because no target database was requested/provided in this phase.
- Before importing into an existing database, run `SITE/database/migrations/2026_06_24_add_import_source_keys.sql` once or rebuild from `schema.sql`.
- Client-approved content is still required before the demo text/links/accounts can be replaced.

## Current Known Limitations

- MySQL-backed runtime is only partially wired for contact messages; expanded import coverage now exists for the rest of the JSON content, but runtime repositories still need to be wired.
- Contact form stores messages in JSON by default; MySQL runtime support is currently wired for contact messages only when `content_storage.driver=mysql`.
- Real camera stream URL and official client-provided village data still need production values.
- Automated screenshot/browser QA is still blocked in this Codex environment by Node/Playwright `EPERM` access to `C:\Users\User\AppData`; manual visual QA remains required before launch.

## Phase 18: Real Weather And Camera Integration Foundation

Added Open-Meteo weather integration foundation and camera stream URL support while keeping admin fallback content safe.

Sources:

- Open-Meteo Weather Forecast API docs: https://open-meteo.com/en/docs
- Bandza coordinates/source context: https://en.wikipedia.org/wiki/Bandza

Added:

- `SITE/includes/weather.php`

Changed:

- `SITE/includes/data.php`: resolves weather through live provider/cache with fallback.
- `SITE/index.php`: weather modal now displays the weather source/update note; camera modal can render a configured stream iframe.
- `SITE/admin/settings.php`: added weather provider/cache/coordinate fields and camera stream URL field.
- `SITE/assets/css/style.css`: added iframe sizing for camera stream embeds.
- `.gitignore`: ignores runtime `SITE/storage/weather-cache.json`.
- `README.md` and `docs/project-checklist.md`: documented Phase 18 status and remaining camera dependency.

How it works:

- Default weather provider is Open-Meteo using Bandza coordinates `42.34889, 42.28417`.
- Weather requests use server-side PHP with a 30 minute cache.
- If Open-Meteo or outbound HTTPS is unavailable, the homepage keeps showing the admin-managed fallback weather instead of failing.
- Admin can switch weather to fallback-only, edit coordinates/cache minutes, and later paste a camera stream/embed URL.

Problems found and fixed:

- Local PHP could not fetch Open-Meteo from this environment even with `allow_url_fopen=1`; a `curl` fallback was added, and the page was verified to degrade to admin fallback cleanly.
- The first homepage smoke script reused PowerShell's read-only `$HOME` variable as `$home`; it was rerun with `$homeResponse`.
- Admin settings initially used live-resolved weather values, which could cause saving current weather into fallback fields. It now uses the raw stored weather config for editing.

Verification:

- `php -l SITE/includes/weather.php`, `SITE/includes/data.php`, `SITE/index.php` and `SITE/admin/settings.php` passed.
- `node --check SITE/assets/js/main.js` passed.
- Local homepage returned 200 and showed the fallback weather source when Open-Meteo could not be reached.
- Authenticated admin settings returned 200 and showed `weather_provider` and `camera_stream_url` controls.

Next phase notes:

- Real camera stream remains WAITING until the client provides the camera provider/embed URL.
- Phase 19 should do manual responsive/browser/accessibility QA, unless client-approved content arrives first.

## Phase 19: Responsive Browser QA Fallback Pass

Ran a responsive/browser QA fallback pass after browser automation remained unavailable in this environment.

Checked:

- Public routes returned 200: home, news, projects, about, history, contact, football and admin login.
- Rendered PHP/HTML contains modal hooks for camera, weather and donation.
- Listing pages contain live filter/sort hooks.
- Contact/admin forms contain CSRF fields where state changes happen.
- Admin settings exposes `weather_provider` and `camera_stream_url` controls added in Phase 18.
- CSS includes focus-visible rules, modal overflow handling, mobile breakpoints, grid collapse rules, stable aspect-ratio media and mobile overflow hardening.

Problems found and fixed:

- Browser automation via node_repl/Playwright still fails with `EPERM` while trying to access `C:\Users\User\AppData`. No product code was changed for this; the phase used HTTP/DOM/CSS checks and kept the manual visual QA task open for a real browser.

Verification:

- HTTP route smoke passed for the main public pages and admin login.
- DOM hook scan passed for modal/filter/sort/form controls.
- CSS responsive/focus/overflow scan passed.

Next phase notes:

- Phase 20 should prepare hosting/deployment instructions, upload permissions, backup/restore and final release checklist.
- A real browser visual pass is still required before production launch.

## Phase 20: Hosting Deployment Prep And Final Release Checklist

Added production deployment documentation so the project can be moved to a PHP/MySQL host without losing the required order of operations.

Added:

- `docs/production-deployment-checklist.md`

Changed:

- `README.md`: linked the deployment checklist and updated the recommended next work.
- `docs/project-checklist.md`: marked deployment instructions, upload permissions, backup/restore and rollback documentation as complete.

Documented:

- PHP/MySQL hosting requirements.
- Production `SITE/includes/config.php` setup.
- Admin password hash generation.
- New database setup from `schema.sql`.
- Existing database migration for import `source_key` columns.
- JSON-to-MySQL dry-run/import order.
- Upload/storage folder permissions.
- Runtime backup list.
- Rollback process by Git commit.
- Launch smoke checks.
- Launch blockers that still require client/hosting decisions.

Verification:

- Documentation was checked against current scripts, schema, migrations, `.gitignore`, README and checklist.
- No product PHP/CSS/JS behavior changed in this phase.

Next phase notes:

- Remaining launch blockers are client-approved content, real donation/contact/social values, production credentials/domain/host, real browser visual QA and final security review after deployment decisions.

## Phase 21: Admin Login And Contact Form Rate Limiting

Added a lightweight file-backed throttling layer for launch-critical forms.

Added:

- `SITE/includes/rate-limit.php`

Changed:

- `SITE/admin/login.php`: admin login now allows 5 attempts per 15 minutes per client address, then shows a retry message. A successful login clears the login bucket.
- `SITE/contact.php`: validated contact form submissions now allow 5 saved-message attempts per 10 minutes per client address.
- `.gitignore`: ignores `SITE/storage/rate-limit.json`, because it is runtime cache state.
- `README.md` and this checklist/worklog were updated for handoff.

How it works:

- The limiter stores hashed client/action keys in `SITE/storage/rate-limit.json`.
- It does not store raw IP addresses.
- It does not require MySQL, so it works in the current JSON development setup and on simple PHP hosting.
- CAPTCHA remains a review item instead of a default dependency; add it only if real spam appears or the client asks for it.

Problems found and fixed:

- The first verification created `SITE/storage/rate-limit.json`; this is expected runtime state, so it was explicitly added to `.gitignore`.
- No PHP syntax or route-level regression was found.

Verification:

- `php -l` passed for `SITE/includes/rate-limit.php`, `SITE/admin/login.php` and `SITE/contact.php`.
- Full PHP lint passed for all 31 PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- A direct PHP behavior test confirmed the limiter allows the configured number of attempts and blocks the next one.
- HTTP smoke checks passed for `/admin/login.php` and `/contact.php`.
- Admin login POST test confirmed repeated wrong passwords trigger the rate-limit message; the test bucket was cleared afterwards.
- `git diff --check` passed with only the existing Windows LF/CRLF warnings.

Next phase notes:

- Admin list pages still need search/filter/sort before the admin UX is comfortable with more content.
- Final security review should happen after storage/deployment decisions are settled.
- If contact spam becomes likely, revisit stronger anti-spam options such as CAPTCHA, server-side challenge fields, or SMTP/provider-level filtering.

## Next Phase

Remaining Production Blockers

Planned:

- Replace demo content and demo financial/contact/social values after client approval.
- Configure real hosting/domain/admin credentials.
- Complete real-browser visual QA and final security review.

## Local Development

Current local server used in this Codex session:

```text
http://127.0.0.1:8082/index.php
```

Typical XAMPP URL may also be:

```text
http://localhost/SITE/
```
