# Banza Site Project Checklist

Updated: 2026-06-24

This checklist is the project control document for finishing the Banza village website. Keep it updated after every phase before committing/pushing.

## Status Legend

- `[DONE]` implemented, tested and pushed.
- `[NEXT]` next practical task.
- `[TODO]` required before production.
- `[IN PROGRESS]` currently being implemented.
- `[PROBLEM]` known bug or risk that must be fixed.
- `[REVIEW]` decide whether to do it.
- `[RETHINK]` likely useful, but check if a better approach exists.
- `[WAITING]` needs client/hosting/provider information.

## Current Baseline

- `[DONE]` Public PHP site scaffold exists under `SITE/`.
- `[DONE]` Admin panel exists under `SITE/admin/`.
- `[DONE]` Dev storage currently uses `SITE/storage/content.json`.
- `[DONE]` Runtime uploads are ignored by Git except `SITE/uploads/.gitkeep`.
- `[DONE]` Worklog exists at `docs/project-worklog.md`.
- `[DONE]` Original full build prompt exists at `docs/banza-site-prompts.md`.
- `[DONE]` GitHub repo is public: `https://github.com/akhelasi/banza-site`.
- `[DONE]` Latest pushed phase before this checklist update: Phase 16, commit `f1d269b`.

## Public Site

- `[DONE]` Home page layout with header, logo, navigation and social links.
- `[DONE]` Hero section with village image, live camera card and weather card.
- `[DONE]` Camera modal opens from home page.
- `[DONE]` Weather modal opens from home page and shows nearby places.
- `[DONE]` Donation sidebar/card and donation accounts modal.
- `[DONE]` Popular projects sidebar/card.
- `[DONE]` Follow/social block.
- `[DONE]` Football team preview on home page and football detail page.
- `[DONE]` Latest news section on home page.
- `[DONE]` About preview and history preview on home page.
- `[DONE]` Public pages: news, news detail, history, projects, about, contact, football.
- `[DONE]` Listing pages have live search/filter/sort without page reload.
- `[DONE]` News detail page supports gallery and YouTube modal hooks.
- `[DONE]` Contact form submits to admin inbox.
- `[TODO]` Add a public-facing projects detail page if the client wants full project articles instead of inline project cards.
- `[TODO]` Add pagination or "load more" for news/projects before real content grows large.
- `[TODO]` Add richer empty states for pages when admin deletes all content.
- `[REVIEW]` Decide whether football content should remain a static page or become a reusable post type/team section.
- `[RETHINK]` Current public design is hand-built CSS. Before adding many more UI states, consider extracting reusable components/classes more aggressively.

## Admin Panel

- `[DONE]` Admin login/logout with password hash and sessions.
- `[DONE]` `/admin` no-trailing-slash redirect bug fixed.
- `[DONE]` Admin dashboard.
- `[DONE]` News CRUD with image upload, gallery uploads and videos.
- `[DONE]` Projects CRUD with featured flag.
- `[DONE]` Static page editing for about, history, football and contact info.
- `[DONE]` Media upload page.
- `[DONE]` Settings management for social links, donation accounts, camera and weather config.
- `[DONE]` Contact messages inbox.
- `[DONE]` Contact messages can be marked read.
- `[DONE]` Trash supports restore and permanent delete for news, projects and contact messages.
- `[DONE]` `post_date` and `last_update` are written in dev storage for admin-managed content.
- `[TODO]` Add admin search/filter/sort to content tables and messages table.
- `[TODO]` Add bulk actions for messages and content lists.
- `[TODO]` Add image alt text fields in admin forms.
- `[TODO]` Add preview links from admin list rows to public pages.
- `[TODO]` Add admin profile/password change flow.
- `[REVIEW]` Decide whether there should be multiple admin users/roles or one shared admin account for launch.
- `[RETHINK]` Current admin is file-backed JSON. For production, moving admin CRUD to MySQL is the cleaner long-term path.

## Content And Data

- `[DONE]` Seed/demo content exists for Banza, news, projects, history, about, football and contact.
- `[DONE]` `SITE/content-sources.md` exists for source notes.
- `[DONE]` `post_date` and `last_update` added to `schema.sql`.
- `[DONE]` MySQL triggers added to fill/update date metadata in `dd/mm/YYYY` format.
- `[TODO]` Replace demo content with verified client-provided Georgian text.
- `[TODO]` Replace demo email/phone/bank/social links with real values.
- `[TODO]` Replace generic/remote image URLs with approved local assets where possible.
- `[TODO]` Add final real population/families/vineyard/elevation data after verification.
- `[TODO]` Add a clear source/provenance note for every researched village fact.
- `[WAITING]` Client must provide real donation account numbers.
- `[WAITING]` Client must provide real social network URLs.
- `[WAITING]` Client must approve or provide final village photos.
- `[REVIEW]` Decide whether internet-researched content is acceptable for launch or should be treated only as draft seed content.

## Database And Storage

- `[DONE]` `schema.sql` contains tables for admins, pages, posts, media, settings, social links, donation accounts and contact messages.
- `[DONE]` `schema.sql` includes indexes for common lookups.
- `[DONE]` Dev JSON storage supports current admin/public flows.
- `[DONE]` Storage direction decided in `docs/storage-decision.md`: JSON for development/approval, MySQL before production.
- `[IN PROGRESS]` Implement MySQL-backed repositories for posts/pages/settings/messages/media.
- `[DONE]` Contact messages have a runtime MySQL repository path behind `content_storage.driver=mysql`.
- `[DONE]` Add first migration/import script from `storage/content.json` to MySQL for contact messages.
- `[DONE]` Expand migration/import script to posts, pages, settings, social links, donation accounts and media.
- `[TODO]` Add install/setup script for creating initial admin and default settings.
- `[TODO]` Add database backup/restore notes for production hosting.
- `[PROBLEM]` Current JSON storage is fine for development but risky for production concurrency, backups and multi-admin editing.
- `[RETHINK]` MySQL migration should be incremental, not an all-at-once rewrite.

## Uploads And Media

- `[DONE]` Image uploads support JPG, PNG, WEBP and GIF.
- `[DONE]` Upload max size is 5MB.
- `[DONE]` Upload validation uses MIME detection and `getimagesize`.
- `[DONE]` Uploaded filenames are randomized and stored under `SITE/uploads/YYYY/MM/`.
- `[DONE]` Permanent delete removes unreferenced uploaded files safely.
- `[TODO]` Add admin image library search/filter.
- `[TODO]` Add alt text and captions for uploaded images.
- `[TODO]` Add image resizing/compression or document hosting-level image optimization.
- `[TODO]` Add max dimensions check to prevent very large images from exhausting memory.
- `[REVIEW]` Decide whether uploaded videos should remain YouTube-only or support local video files.

## Security

- `[DONE]` State-changing admin forms use CSRF checks.
- `[DONE]` Admin login uses `password_verify`.
- `[DONE]` Output escaping uses `e()` / `htmlspecialchars`.
- `[DONE]` Upload handling validates file type/size.
- `[DONE]` Raw stack/database errors are not intentionally shown to users.
- `[TODO]` Change demo admin credentials before any public deployment. Hash generator is available at `SITE/scripts/generate-password-hash.php`.
- `[DONE]` Keep real production config in untracked `SITE/includes/config.php`.
- `[TODO]` Add rate limiting or throttling for admin login and contact form.
- `[TODO]` Add stronger spam protection for contact form if spam becomes likely.
- `[DONE]` Add basic PHP security headers in public/admin layout bootstrap.
- `[DONE]` Add configurable session cookie name, HttpOnly, SameSite and HTTPS secure flag.
- `[TODO]` Run a final security review after MySQL wiring.
- `[WAITING]` Real production admin email/password must be chosen by the client before deployment.
- `[REVIEW]` Decide whether to add CAPTCHA; avoid it unless spam actually appears or the client requests it.

## Weather, Camera And External Integrations

- `[DONE]` Camera/weather UI and modals exist.
- `[DONE]` Camera/weather values are admin-editable as demo/config content.
- `[DONE]` Add camera stream/embed URL support in admin and homepage modal.
- `[WAITING]` Replace camera preview with real live stream/embed once the camera is purchased/installed.
- `[DONE]` Choose weather source/API and implement live weather fetch/cache.
- `[DONE]` Add graceful fallback when weather/camera provider is unavailable.
- `[WAITING]` Client must provide camera stream URL/provider.
- `[WAITING]` Client must approve weather provider/API key approach.
- `[REVIEW]` Decide whether live weather should be server-side cached or browser-side fetched.
- `[RETHINK]` For weather, server-side caching is likely better than direct browser API calls because it protects API keys and reduces rate-limit risk.

## Email And Notifications

- `[DONE]` Contact messages are stored and visible in admin.
- `[TODO]` Add optional SMTP email notification when a contact message is submitted.
- `[TODO]` Add admin setting for notification recipient email.
- `[TODO]` Add failure-safe behavior if SMTP fails but message storage succeeds.
- `[WAITING]` Client/host must provide SMTP credentials or email provider.
- `[REVIEW]` Decide whether email notification is required for launch or admin inbox is enough initially.

## Frontend QA And Accessibility

- `[DONE]` Public route smoke checks passed in previous phases.
- `[DONE]` Search/filter/sort hooks verified.
- `[DONE]` Main JS syntax checks passed.
- `[TODO]` Manual responsive QA on desktop, tablet and mobile.
- `[TODO]` Manual browser QA for modals, contact form, admin forms and upload UI.
- `[TODO]` Keyboard navigation pass for header nav, modals, forms and admin actions.
- `[TODO]` Screen reader label/heading pass.
- `[TODO]` Check all Georgian text for overflow on small screens.
- `[PROBLEM]` Automated Playwright/in-app browser QA is blocked in this environment by local `EPERM` filesystem permission errors.
- `[RETHINK]` If automated browser QA remains blocked locally, use a lightweight manual QA checklist or run Playwright from a normal terminal outside Codex.

## Deployment And Operations

- `[DONE]` GitHub repo stores source code.
- `[DONE]` GitHub Pages was intentionally not used because PHP will not run there.
- `[TODO]` Pick production hosting that supports PHP and MySQL.
- `[DONE]` Prepare production config instructions.
- `[TODO]` Prepare upload folder permissions instructions.
- `[TODO]` Prepare deployment checklist for clone/install/config/database/import/admin password.
- `[TODO]` Add backup plan for database and uploaded media.
- `[TODO]` Add rollback plan for code deploys.
- `[WAITING]` Need hosting provider details.
- `[WAITING]` Need production domain/subdomain.
- `[REVIEW]` Decide whether to deploy manually first or add GitHub Actions/FTP/SSH deploy workflow later.

## Documentation And Handoff

- `[DONE]` `docs/project-worklog.md` records completed phases and QA.
- `[DONE]` `docs/banza-site-prompts.md` stores the main project prompt.
- `[DONE]` This checklist exists at `docs/project-checklist.md`.
- `[DONE]` Update README with current setup, admin login, dev server and production notes.
- `[DONE]` Add "continue in Codex" handoff section for workplace continuation.
- `[DONE]` Document how to migrate runtime uploads.
- `[DONE]` Document how to replace demo content safely.
- `[DONE]` Document final release checklist.
- `[REVIEW]` Decide whether README should stay Georgian-only or Georgian + English technical notes.

## Recommended Remaining Phase Order

1. `[DONE]` Phase 12: README/setup/handoff cleanup so workplace continuation is frictionless.
2. `[DONE]` Phase 13: Decide storage direction: keep JSON for approval phase or wire MySQL now.
3. `[DONE]` Phase 14: Start incremental MySQL-backed CRUD and import script.
4. `[DONE]` Phase 15: Expand MySQL runtime repository slice while keeping JSON fallback.
5. `[DONE]` Phase 16: Production config, admin password change workflow, security hardening.
6. `[WAITING]` Phase 17: Replace demo content/assets with client-approved content.
7. `[DONE]` Phase 18: Real weather/camera integration foundation.
8. `[NEXT]` Phase 19: Responsive/manual browser QA and accessibility pass.
9. `[TODO]` Phase 20: Hosting deployment prep and final release checklist.

## Definition Of Done For Production

- `[TODO]` All public pages work without PHP warnings/notices.
- `[TODO]` Admin can manage all launch-critical content.
- `[TODO]` Uploads, trash, contact messages and settings are verified after deployment.
- `[TODO]` Demo credentials are replaced with a generated password hash in untracked production config.
- `[TODO]` Real content, real links and real donation accounts are approved by the client.
- `[TODO]` Database and uploads have backup/restore instructions.
- `[TODO]` Mobile and desktop manual QA is complete.
- `[TODO]` Security review is complete after final storage/deployment decisions.
- `[TODO]` GitHub `main` contains final source and worklog/checklist are updated.
