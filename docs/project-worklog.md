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

## Phase 22: Admin Table Search, Filter And Sort

Added live table controls to the admin screens that become hard to use once real content grows.

Changed:

- `SITE/admin/content.php`
  - Added live search/sort for static pages.
  - Added live search, category/status filter and title/date sort for news and projects.
- `SITE/admin/messages.php`
  - Added live search, read/unread filter and sender/date sort for contact messages.
- `SITE/admin/trash.php`
  - Added live search, item type filter and title/deleted-date sort for trash.
- `SITE/assets/js/main.js`
  - Extended date sorting to support both ISO dates and `dd/mm/YYYY` metadata values.
- `SITE/assets/css/style.css`
  - Added admin filter spacing.

How it works:

- Reused the existing `data-live-filter` JavaScript already used by public listing pages.
- Table rows now carry `filter-item`, `data-title`, `data-text`, `data-category`, `data-sort-title` and `data-sort-date` attributes.
- Filtering and sorting happen in the browser, so the page does not reload and scroll position is not reset.

Problems found and fixed:

- Admin content could sort against `post_date` values in `dd/mm/YYYY` format. The JS sort parser now handles that format as well as normal ISO dates.
- HTTP auth smoke with PowerShell/curl was unreliable in this Codex environment. A direct localhost header check still worked, and a PHP CLI render check confirmed the admin content list renders the filter hooks. Full real-browser admin interaction QA remains in the manual QA checklist.

Verification:

- `php -l` passed for changed admin PHP files.
- Full PHP lint passed for all 31 PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- Source hook scan confirmed each admin filter target has a matching table body id.
- CLI render check confirmed the admin news list renders `data-live-filter` and `filter-item` hooks.

Next phase notes:

- Media library search/filter remains a separate TODO because it uses a thumbnail grid and needs a slightly different UX.
- Bulk actions for admin tables are still open.
- Real browser QA should confirm keyboard focus, overflow and table usability on small screens.

## Phase 23: Production Setup Script

Added a CLI setup script for the first production/dev MySQL bootstrap after `schema.sql` is loaded.

Added:

- `SITE/scripts/setup-production.php`

Changed:

- `.gitignore`: ignores root-level `Microsoft/` PowerShell cache artifacts generated by this Windows environment.
- `README.md`: documents setup dry-run and production setup command.
- `docs/production-deployment-checklist.md`: adds the setup script to deployment order.
- `docs/project-checklist.md`: marks the install/setup script item complete.

How it works:

- The script is CLI-only.
- Required input: `--email` plus either `--password` or `--password-env`.
- Passwords must be at least 12 characters and are hashed with `password_hash`.
- In real mode it opens MySQL through the existing config, starts a transaction, creates or updates the `admins` row, and seeds default `settings`, `social_links` and `donation_accounts` from `SITE/storage/content.json`.
- `--dry-run` validates input and content JSON and reports planned counts without connecting to MySQL.
- `--force` is required before overwriting an existing admin row.

Problems found and fixed:

- A root `Microsoft/Windows/PowerShell/ModuleAnalysisCache` folder was generated by local PowerShell tooling. It is now ignored so it cannot be accidentally committed.
- The runtime admin login still uses `SITE/includes/config.php`; the setup script prints a clear note that DB-backed admin auth is not enabled yet.

Verification:

- `php -l SITE/scripts/setup-production.php` passed.
- `php SITE/scripts/setup-production.php --help` passed.
- `php SITE/scripts/setup-production.php --email=admin@example.com --password-env=BANZA_ADMIN_PASSWORD --dry-run` passed with a temporary environment variable.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.

Next phase notes:

- DB-backed admin authentication is still a separate future phase if production should use the `admins` table directly.
- Demo runtime credentials still must be replaced in untracked `SITE/includes/config.php` before launch.

## Phase 24: Admin Media Library Search, Filter And Sort

Added live controls to the admin media library so uploaded and seed images remain usable as the file list grows.

Changed:

- `SITE/admin/media.php`
  - Added uploaded-image search by filename/path.
  - Added uploaded-image extension filter for JPG, JPEG, PNG, WEBP and GIF.
  - Added uploaded-image sorting by date, name and file size.
  - Added seed asset search, type filter and name sort.
- `SITE/assets/js/main.js`
  - Added numeric `size` sorting support for filterable items.
- `docs/project-checklist.md`
  - Marked admin image library search/filter complete.

How it works:

- Reuses the same `data-live-filter` client-side system as public listing pages and admin content tables.
- Media cards now expose filter/sort metadata through `data-title`, `data-text`, `data-category`, `data-sort-title`, `data-sort-date` and `data-sort-size`.
- Filtering and sorting stay client-side, with no page reload and no scroll reset.

Problems found and fixed:

- The existing sort helper only handled text/date fields. File-size sorting needed a numeric branch, so `main.js` now supports `size-asc` and `size-desc`.

Verification:

- `php -l SITE/admin/media.php` passed.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- Source hook scan confirmed uploaded and seed media filter targets exist.

Next phase notes:

- Alt text/captions for uploaded images remain open and should be handled with a persistence decision, not only form fields.
- Image resizing/compression and max-dimension validation are still open media hardening tasks.

## Phase 25: Upload Max-Dimensions Validation

Added a server-side dimensions guard for uploaded images.

Changed:

- `SITE/includes/uploads.php`
  - Added `upload_max_dimensions()` with a 6000x6000px limit.
  - Validated image width and height immediately after `getimagesize`.
  - Rejects images that exceed the configured pixel dimensions before moving them into `SITE/uploads/`.
- `README.md`
  - Notes that upload validation includes type, file size and dimensions.
- `docs/project-checklist.md`
  - Marks max-dimensions upload protection complete.

How it works:

- The existing upload flow already validates upload errors, 5MB file size, MIME type and image validity.
- The new check reads the decoded dimensions from `getimagesize` and returns a user-safe error if either side is larger than 6000px.

Problems found and fixed:

- No regression was found in the upload validation path during this phase.

Verification:

- `php -l SITE/includes/uploads.php` passed.
- Full PHP lint passed for all PHP files under `SITE/`.
- A direct PHP check confirmed `upload_max_dimensions()` returns `6000x6000`.
- `node --check SITE/assets/js/main.js` passed.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Image resizing/compression remains open. Dimension rejection protects the server, but it does not optimize accepted uploads.
- Alt text/captions still need a persistence model.

## Phase 26: Admin List Preview Links

Added public preview links from admin content list rows.

Changed:

- `SITE/admin/content.php`
  - Added `admin_preview_url()` to map admin content rows to public URLs.
  - Static pages now include a `ნახვა` action that opens about/history/football/contact public pages.
  - News rows now include a public preview link to `news-detail.php?slug=...`.
  - Project rows now include a public preview link to `projects.php#slug`.
- `docs/project-checklist.md`
  - Marked admin preview links complete.

How it works:

- Preview links open in a new tab with `target="_blank"` and `rel="noopener"`.
- URLs are generated server-side and escaped before rendering.

Problems found and fixed:

- No regression was found during this phase.

Verification:

- `php -l SITE/admin/content.php` passed.
- Full PHP lint passed for all PHP files under `SITE/`.
- Source scan confirmed preview links are rendered for pages and content rows.
- `node --check SITE/assets/js/main.js` passed.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Bulk actions and admin profile/password change flow remain open admin panel tasks.

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

## Phase 27: Admin Profile And Password Change Flow

Added an authenticated admin profile page for changing the admin email/password without committing secrets.

Added:

- `SITE/admin/profile.php`

Changed:

- `SITE/includes/auth.php`
  - Added helpers to write admin credentials into ignored runtime `SITE/includes/config.php`.
  - Added current session email refresh after credential changes.
- `SITE/includes/admin-layout.php`
  - Added `პროფილი` to the admin sidebar.
- `README.md`
  - Added the profile page to current status.
- `docs/project-checklist.md`
  - Marked admin profile/password change flow complete.

How it works:

- The form requires CSRF, the current password, a valid email, a new password of at least 12 characters and matching confirmation.
- It verifies the current password with `password_verify`.
- It hashes the new password with `password_hash`.
- It writes the updated config array to `SITE/includes/config.php`, which is already ignored by Git.
- It never writes real credentials to committed files.

Problems found and fixed:

- The flow needed to match the current config-backed auth model. It intentionally updates runtime config, not the `admins` MySQL table, because login does not read the table yet.
- An initial patch duplicated helper functions in `auth.php`; this was caught in source scan and removed before committing.

Verification:

- `php -l` passed for `SITE/admin/profile.php`, `SITE/includes/auth.php` and `SITE/includes/admin-layout.php`.
- Full PHP lint passed for all 33 PHP files under `SITE/`.
- Source scan confirmed the profile nav entry, CSRF field, current-password check and runtime config writer exist exactly once.
- CLI render check confirmed the profile form renders for an authenticated admin session.
- `node --check SITE/assets/js/main.js` passed.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- DB-backed admin authentication remains a future phase if production should use the `admins` table directly.
- Bulk actions for admin tables remain open.
## Phase 28: Admin Bulk Actions

Added bulk operations for the admin lists that are most likely to grow during normal use.

Changed:

- `SITE/admin/content.php`
  - Added `posted_slugs()` helper.
  - Added bulk soft-delete for selected news/project rows.
  - Added row checkboxes and a bulk delete form using HTML `form` attributes, so existing per-row forms remain valid.
- `SITE/admin/messages.php`
  - Added `posted_message_slugs()` helper.
  - Added bulk mark-read and bulk soft-delete for selected contact messages.
  - Supports both JSON storage and the existing MySQL contact-message repository path.
- `SITE/assets/css/style.css`
  - Added `.admin-bulk-bar` styles for compact bulk action controls.
- `README.md`
  - Added bulk actions to the current status.
- `docs/project-checklist.md`
  - Marked bulk actions complete.

How it works:

- Bulk forms submit selected `slugs[]` values with CSRF protection.
- Content bulk delete sets `deleted_at`, updates content dates and sends rows to trash.
- Message bulk mark-read sets `read_at`; message bulk delete sets `deleted_at` and sends rows to trash.
- Empty selections and already-processed/missing rows return user-safe admin flash errors.

Problems found and fixed:

- The first multi-file patch failed because the Windows sandbox helper could not read `main.js`. The phase was completed with targeted replacements and follow-up source scans.

Verification:

- `php -l` passed for `SITE/admin/content.php` and `SITE/admin/messages.php`.
- Full PHP lint passed for all PHP files under `SITE/`.
- Source scan confirmed bulk helpers, bulk forms, row checkboxes and CSRF fields exist.
- `node --check SITE/assets/js/main.js` passed.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Bulk select-all JavaScript can be added later, but the current implementation already supports real multi-select bulk actions without invalid nested forms.
- Alt text/captions and image optimization remain open media tasks.

## Phase 29: Admin Image Alt Text Fields

Added admin-editable alternative text for content images so public pages can use meaningful image descriptions instead of always falling back to titles.

Changed:

- `SITE/admin/content.php`
  - Added `image_alt` storage for news and project main images.
  - Added `gallery_alt` storage for news gallery images.
  - Added `image_alt` storage for the football static page image.
  - Added admin form fields for these values.
- `SITE/index.php`
  - Homepage football and latest-news images now use `image_alt` when available.
- `SITE/news.php`
  - News cards now use `image_alt` when available.
- `SITE/news-detail.php`
  - Main news image uses `image_alt`.
  - Gallery thumbnails and lightbox labels use `gallery_alt`, then `image_alt`, then title as fallback.
- `SITE/projects.php`
  - Project cards now use `image_alt` when available.
- `SITE/football.php`
  - Football page image now uses `image_alt` when available.
- `README.md` and `docs/project-checklist.md`
  - Documented Phase 29 and updated remaining media tasks.

How it works:

- Existing content remains compatible because every public render has a title fallback.
- Admin-entered values are trimmed before storage.
- Output remains escaped through the existing `e()` helper.

Problems found and fixed:

- A PowerShell write inserted a UTF-8 BOM at the start of `SITE/admin/content.php`; it was detected in `git diff` as `﻿<?php` and rewritten as UTF-8 without BOM before verification.
- The Windows sandbox intermittently failed with ACL helper errors, so targeted workspace edits were rerun with escalation where necessary.

Verification:

- `php -l` passed for changed PHP files.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- Source scan confirmed `image_alt` and `gallery_alt` are present in admin save/forms and public render paths.
- Localhost smoke checks returned 200 for home, news, projects and football pages.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Uploaded-media captions remain open as a separate task because captions need a clearer persistence model than the current per-content image alt fields.
- Image resizing/compression remains open for later media optimization.

## Phase 30: Public Project Detail Page

Added a dedicated public detail page for projects so project cards can lead to full content instead of only inline summaries on the listing page.

Changed:

- `SITE/project-detail.php`
  - New project detail route with slug validation, 404 fallback, hero, status/category metadata, main image and full body paragraphs.
- `SITE/projects.php`
  - Project cards now link to the detail page.
  - Listing text now safely handles both string and array project bodies.
- `SITE/index.php`
  - Popular-project sidebar links now open the project detail page.
- `SITE/admin/content.php`
  - Project preview links now point to the public project detail page.
- `SITE/includes/helpers.php`
  - Added `content_paragraphs()` to normalize string or array content for article rendering.
- `SITE/assets/css/style.css`
  - Added a small rule so linked project cards keep their existing card dimensions.
- `README.md` and `docs/project-checklist.md`
  - Documented Phase 30 and marked the public project detail task complete.

How it works:

- `project-detail.php?slug=...` accepts only lowercase Latin slugs with numbers and dashes.
- Missing/deleted projects return HTTP 404 with a user-safe fallback page.
- Project body content is rendered as paragraphs whether it comes from seed string content or admin-managed array content.

Problems found and fixed:

- Project listing previously assumed `body` was always a string. The new listing uses `plain_text()` for filter text and card summaries, preventing an `Array` rendering issue after admin-edited projects.

Verification:

- `php -l` passed for changed PHP files.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- Localhost smoke checks returned 200 for projects listing and a project detail page, and 404 for a missing project slug.
- Source scan confirmed listing/sidebar/admin preview links target `project-detail.php`.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Richer empty states remain open for cases where admin deletes all public content.

## Phase 31: News And Projects Load More

Added client-side load-more pagination to public news and project listings while preserving the existing live search/filter/sort behavior.

Changed:

- `SITE/news.php`
  - Added `data-page-size` and a load-more button for the news grid.
- `SITE/projects.php`
  - Added `data-page-size` and a load-more button for the project grid.
- `SITE/assets/js/main.js`
  - Extended the existing live filter controller to support optional page size and load-more controls.
  - Filter/search/sort changes reset the visible limit so new result sets start cleanly.
- `SITE/assets/css/style.css`
  - Added load-more button spacing and hidden-state styling.
- `README.md` and `docs/project-checklist.md`
  - Documented Phase 31 and marked listing pagination complete.

How it works:

- The first 6 matching items are shown on news/projects pages.
- Clicking "მეტის ჩვენება" reveals the next 6 matching items without reloading the page.
- Search, category/status filter and sort still happen live, and changing any of them resets the load-more limit.

Problems found and fixed:

- The existing live filter used one `hidden` state for filtering. The implementation now computes matched rows first, then applies the page-size limit only to those matched rows, so empty-state logic stays correct.

Verification:

- `php -l` passed for changed PHP files.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- Source scan confirmed page-size/load-more hooks exist on news/projects and JS support is wired.
- Localhost smoke checks returned 200 for news and projects pages.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Richer empty states remain open for cases where admin deletes all public content.
- Manual real-browser QA should still verify load-more interaction visually on mobile and desktop.

## Phase 32: Homepage Empty States

Added explicit homepage empty states for sections that can become blank after admin deletes all public content.

Changed:

- `SITE/index.php`
  - Reuses `$latestNews` and `$popularProjects` slices for cleaner rendering.
  - Shows a reader-facing empty message if there are no latest news items.
  - Shows a reader-facing empty message if there are no popular projects.
- `README.md` and `docs/project-checklist.md`
  - Documented Phase 32 and marked richer empty states complete.

How it works:

- The homepage still renders normally when content exists.
- If news or projects are empty, the page shows a clear message instead of leaving a blank grid/list.
- Messages are public-safe and do not expose private data.

Problems found and fixed:

- No product bug was found during this phase; this closes a known empty-content UX gap.

Verification:

- `php -l SITE/index.php` passed.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- Source scan confirmed homepage empty-state messages exist.
- Localhost homepage smoke check returned 200.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Manual real-browser QA should verify homepage empty states after temporarily clearing content in a local/dev copy.
- Remaining content-related blockers now mainly depend on client-approved real text, images, social links and donation accounts.

## Phase 33: Optional Upload Image Optimization

Added best-effort server-side optimization for uploaded images.

Changed:

- `SITE/includes/uploads.php`
  - Added `upload_optimization_config()`.
  - Added `optimize_uploaded_image()` for JPG, PNG and WEBP files when PHP GD is available.
  - Calls optimization after a validated uploaded file is moved into `SITE/uploads/`.
- `SITE/admin/media.php`
  - Upload help text now notes that large JPG/PNG/WEBP files can be automatically reduced when GD is enabled.
- `README.md` and `docs/project-checklist.md`
  - Documented Phase 33 and marked image optimization complete.

How it works:

- Existing validation still runs first: upload error, file size, `is_uploaded_file`, MIME type, `getimagesize` and max dimensions.
- GIF files are intentionally not rewritten so animation is not broken.
- JPG/PNG/WEBP files are resized to fit within 2400x2400px when needed.
- If an image is already small, the optimizer only replaces it when the rewritten file is smaller.
- If GD is unavailable or optimization fails, the upload remains successful and keeps the original validated file.

Problems found and fixed:

- The local PHP runtime reports `gd no`, so real resizing cannot be exercised in this Codex environment. The implementation is deliberately a no-op without GD instead of blocking uploads.
- A previous PowerShell edit attempt timed out before inserting the optimizer call; source scans confirmed the missing call and it was added with a direct patch.

Verification:

- `php -l` passed for changed PHP files.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- Source scan confirmed the optimization config, optimizer function and upload-flow call exist.
- Local PHP runtime check confirmed GD is currently unavailable, so runtime resize/compression needs a host/dev environment with GD enabled.
- Direct helper check confirmed `optimize_uploaded_image()` returns `false` safely when GD is unavailable.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Per-media captions remain open and need a storage model.
- Production hosting should enable PHP GD if automatic upload optimization is desired.

## Phase 34: Uploaded Media Alt And Caption Metadata

Added editable metadata for uploaded media library images.

Changed:

- `SITE/includes/content-store.php`
  - Added `mediaItems` to JSON storage defaults.
- `SITE/includes/data.php`
  - Exposes `$mediaItems` from the content store.
- `SITE/admin/media.php`
  - Added a `save_meta` POST action with CSRF validation.
  - Uploaded media cards now render editable alt text and caption fields.
  - Media preview images use saved alt text when available.
- `SITE/assets/css/style.css`
  - Added compact styles for per-media metadata forms.
- `SITE/database/schema.sql`
  - Added `media.caption`.
- `SITE/database/migrations/2026_06_24_add_media_caption.sql`
  - Adds the caption column to existing databases.
- `SITE/includes/repositories/content-import-repository.php`
  - Imports standalone `mediaItems` records into MySQL media rows.
  - Adds caption binding for imported post gallery/video media.
- `SITE/scripts/import-json-to-mysql.php`
  - Added `media_items` as a supported import target.
- `README.md`, `docs/production-deployment-checklist.md` and `docs/project-checklist.md`
  - Documented the new migration/import target and marked the task complete.

How it works:

- Metadata is keyed by uploaded file path under `content.json.mediaItems`.
- Only real uploaded `uploads/...` paths can be edited through the media metadata action.
- Saved metadata records include `path`, `alt`, `caption` and `last_update`.
- MySQL import stores uploaded media metadata as standalone `media` records with nullable `post_id`.

Problems found and fixed:

- The first implementation used `$content` in `media.php` before setting it locally. This was fixed by initializing `$content = $contentStore ?? []`.
- The first metadata save guard accepted any `uploads/...` string. It now validates the submitted path against the actual uploaded media list before saving.
- The production schema had `alt_text` but no caption column, so an additive schema field and migration were added.

Verification:

- `php -l` passed for changed PHP files.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- `php SITE/scripts/import-json-to-mysql.php --dry-run --only=media_items` passed.
- Source scan confirmed `mediaItems`, `save_meta`, `media-meta-form`, `caption` schema/import bindings and the new migration exist.
- Local HTTP smoke with Node `fetch` confirmed `/admin/media.php` returns `302` to `/admin/login.php` when unauthenticated, not a PHP/server error.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Media captions are now stored in the media library; public article/gallery caption rendering can be added later if the client wants captions visible on public pages.
- Remaining production blockers mostly depend on client-approved content, production credentials, hosting and real browser QA.

## Phase 35: Content Source And Provenance Metadata

Added source/provenance metadata so demo, researched seed and client-approved content can be separated clearly during the approval and launch process.

Changed:

- `SITE/includes/data.php`
  - Added `source_status` and `source_note` to seed news, projects and static pages.
  - Marked researched village/history seed facts separately from demo placeholders.
- `SITE/admin/content.php`
  - Added `source_status` and `source_note` fields to news/project and static page forms.
  - Added source status/note visibility to admin content lists.
  - New admin-created content defaults to `client_approved`.
- `SITE/includes/layout.php`
  - Added a reusable `render_source_note()` helper.
- `SITE/about.php`, `SITE/history.php`, `SITE/football.php`, `SITE/contact.php`, `SITE/news-detail.php` and `SITE/project-detail.php`
  - Render source/provenance notes where content can still be researched seed or demo content.
- `SITE/assets/css/style.css`
  - Styled public source notes as compact non-blocking notices.
- `SITE/database/schema.sql`
  - Added `source_status` and `source_note` to `pages` and `posts`.
- `SITE/database/migrations/2026_06_25_add_content_source_metadata.sql`
  - Adds the provenance columns to existing MySQL databases.
- `SITE/includes/repositories/content-import-repository.php`
  - Imports source metadata for pages and posts.
- `SITE/content-sources.md`, `README.md`, `docs/production-deployment-checklist.md` and `docs/project-checklist.md`
  - Documented source metadata and the new migration.

How it works:

- `source_status` accepts `demo`, `researched` or `client_approved`.
- `source_note` stores a short human-readable provenance/approval note.
- Public source notes are visible on static pages and detail pages when metadata exists.
- MySQL imports keep the metadata in first-class columns rather than only inside page body JSON.

Problems found and fixed:

- The schema edit partially applied before the migration file was created, so the migration was added as a separate patch and then verified by source scan.
- Detail pages originally would have hidden provenance for demo/researched news and projects; `render_source_note()` is now called on news and project detail pages too.

Verification:

- `php -l` passed for the new render-smoke helper.
- Full PHP lint passed for all PHP files under `SITE/`.
- `node --check SITE/assets/js/main.js` passed.
- `SITE/storage/content.json` parsed successfully.
- `php SITE/scripts/import-json-to-mysql.php --dry-run --only=pages` passed.
- `php SITE/scripts/import-json-to-mysql.php --dry-run --only=posts` passed.
- Source scan confirmed `source_status`, `source_note`, `render_source_note()` and the new migration are wired through admin, public pages, seed data, imports and docs.
- `php SITE/scripts/render-smoke.php` rendered the changed public routes and confirmed source-note markup on each route.
- `git diff --check` passed with only Windows LF/CRLF warnings.

Next phase notes:

- Real client-approved text, contact values, donation accounts and approved images still need client input.
- When content is approved, admins should switch relevant items to `client_approved` and update `source_note`.

## Phase 36: Public Route Render Smoke Coverage

Expanded the render smoke helper into a broader public route QA check.

Changed:

- `SITE/scripts/render-smoke.php`
  - Now covers `index.php`, `news.php`, `projects.php`, `about.php`, `history.php`, `football.php`, `contact.php`, `news-detail.php` and `project-detail.php`.
  - Runs each route in a separate PHP subprocess so page-level `require` calls cannot collide through function redeclarations.
  - Supports per-route required markup checks via `--contains`.
  - Converts unsuppressed PHP warnings/notices into failures during smoke checks.
  - Uses a writable temp session path for CLI smoke runs.

Problems found and fixed:

- The first smoke expansion failed on the Open-Meteo request because `@file_get_contents()` intentionally suppresses network/SSL warnings for fallback behavior, but the smoke error handler was converting suppressed warnings into fatal errors. The handler now ignores suppressed errors and still fails on real unsuppressed warnings/notices.

Verification:

- `php -l SITE/scripts/render-smoke.php` passed.
- `php SITE/scripts/render-smoke.php` passed for all covered public routes.
- The helper confirms `main-content` on listing/home pages and `source-note` on provenance-enabled pages/detail pages.

Next phase notes:

- This is still a render-level smoke check, not a replacement for final real-browser desktop/mobile QA.
- Real browser QA remains open because the local automated browser stack has been unreliable in this environment.
