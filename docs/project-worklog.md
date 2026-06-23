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


## Current Known Limitations

- MySQL-backed CRUD is not wired yet; current CRUD uses JSON storage for development.
- Contact form stores messages in JSON development storage; SMTP/email notifications are not wired yet.
- Real weather API/live camera feed integration and official client-provided village data still need production values.

## Next Phase

Phase 14: Incremental MySQL Migration

Planned:

- Add repository structure and keep JSON as the default fallback.
- Start MySQL-backed CRUD/import with one low-risk content area.
- Wire SMTP/email notifications for contact messages if needed.
- Replace demo weather/live camera values with production integrations.
- Complete manual responsive browser QA in the target browser.
- Prepare hosting/deployment notes for the real PHP host.

## Local Development

Current local server used in this Codex session:

```text
http://127.0.0.1:8082/index.php
```

Typical XAMPP URL may also be:

```text
http://localhost/SITE/
```
