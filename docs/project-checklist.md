# Banza Site Project Checklist

Updated: 2026-06-26

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
- `[DONE]` Latest completed phase before this checklist update: Phase 52.

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
- `[DONE]` Public pages: news, news detail, project detail, history, projects, about, contact, football.
- `[DONE]` Listing pages have live search/filter/sort without page reload.
- `[DONE]` News detail page supports gallery and YouTube modal hooks.
- `[DONE]` Contact form submits to admin inbox.
- `[DONE]` Add a public-facing projects detail page if the client wants full project articles instead of inline project cards.
- `[DONE]` Add pagination or "load more" for news/projects before real content grows large.
- `[DONE]` Add richer empty states for pages when admin deletes all content.
- `[DONE]` Launch decision: football remains a static managed page; revisit reusable team post type only if recurring team content is needed.
- `[DONE]` Launch decision: keep current hand-built CSS; extract reusable components only if future UI scope grows.

## Admin Panel

- `[DONE]` Admin login/logout with password hash and sessions.
- `[DONE]` Admin login supports MySQL `admins` table when `content_storage.driver=mysql`, with config fallback.
- `[DONE]` `/admin` no-trailing-slash redirect bug fixed.
- `[DONE]` Admin dashboard.
- `[DONE]` News CRUD with image upload, gallery uploads and videos.
- `[DONE]` Projects CRUD with featured flag.
- `[DONE]` Static page editing for about, history, football and contact info.
- `[DONE]` Media upload page.
- `[DONE]` Settings management for social links, donation accounts, camera and weather config.
- `[DONE]` Admin save flows surface JSON/MySQL sync failures instead of silently showing success.
- `[DONE]` Contact messages inbox.
- `[DONE]` Contact messages can be marked read.
- `[DONE]` Trash supports restore and permanent delete for news, projects and contact messages.
- `[DONE]` `post_date` and `last_update` are written in dev storage for admin-managed content.
- `[DONE]` Add admin search/filter/sort to content tables and messages table.
- `[DONE]` Add bulk actions for messages and content lists.
- `[DONE]` Add image alt text fields in admin forms.
- `[DONE]` Add preview links from admin list rows to public pages.
- `[DONE]` Add admin profile/password change flow.
- `[DONE]` Launch decision: use one production admin account for launch; revisit roles when multiple editors need access.
- `[DONE]` Launch decision: keep JSON editing source with MySQL sync for handoff; revisit fully MySQL-native admin CRUD after hosting/multi-admin needs are clear.

## Content And Data

- `[DONE]` Seed/demo content exists for Banza, news, projects, history, about, football and contact.
- `[DONE]` `SITE/content-sources.md` exists for source notes.
- `[DONE]` `post_date` and `last_update` added to `schema.sql`.
- `[DONE]` MySQL triggers added to fill/update date metadata in `dd/mm/YYYY` format.
- `[TODO]` Replace demo content with verified client-provided Georgian text.
- `[TODO]` Replace demo email/phone/bank/social links with real values.
- `[TODO]` Replace generic/remote image URLs with approved local assets where possible.
- `[TODO]` Add final real population/families/vineyard/elevation data after verification.
- `[DONE]` Add source/provenance status and notes for researched/demo seed content.
- `[DONE]` Add launch content audit script for demo placeholders, generic links/accounts and unapproved content statuses.
- `[WAITING]` Client must provide real donation account numbers.
- `[WAITING]` Client must provide real social network URLs.
- `[WAITING]` Client must approve or provide final village photos.
- `[DONE]` Launch decision: internet-researched content is draft seed content until the client approves final copy/data/assets.

## Database And Storage

- `[DONE]` `schema.sql` contains tables for admins, pages, posts, media, settings, social links, donation accounts and contact messages.
- `[DONE]` `schema.sql` includes indexes for common lookups.
- `[DONE]` Dev JSON storage supports current admin/public flows.
- `[DONE]` Storage direction decided in `docs/storage-decision.md`: JSON for development/approval, MySQL before production.
- `[IN PROGRESS]` Implement MySQL-backed repositories for posts/pages/settings/messages/media.
- `[DONE]` Contact messages have a runtime MySQL repository path behind `content_storage.driver=mysql`.
- `[DONE]` Add first migration/import script from `storage/content.json` to MySQL for contact messages.
- `[DONE]` Expand migration/import script to posts, pages, settings, social links, donation accounts and media.
- `[DONE]` Add install/setup script for creating initial admin and default settings.
- `[DONE]` Add database backup/restore notes for production hosting.
- `[DONE]` Add MySQL runtime reader for settings, social links and donation accounts with JSON fallback.
- `[DONE]` Add MySQL runtime reader for static pages and standalone media metadata with JSON fallback.
- `[DONE]` Add MySQL runtime reader for news/projects, including category, display status, date label, gallery and video metadata.
- `[DONE]` Add MySQL write-through sync after admin JSON content saves.
- `[DONE]` Add production MySQL smoke-check helper for host/dev database verification.
- `[WAITING]` Run `SITE/scripts/check-mysql-smoke.php` on the production host or a real dev MySQL database.
- `[PROBLEM]` Current JSON storage is fine for development but risky for production concurrency, backups and multi-admin editing.
- `[DONE]` Launch decision: keep MySQL migration incremental; avoid full admin CRUD rewrite until hosting/multi-admin needs are clear.

## Uploads And Media

- `[DONE]` Image uploads support JPG, PNG, WEBP and GIF.
- `[DONE]` Upload max size is 5MB.
- `[DONE]` Upload validation uses MIME detection and `getimagesize`.
- `[DONE]` Uploaded filenames are randomized and stored under `SITE/uploads/YYYY/MM/`.
- `[DONE]` Permanent delete removes unreferenced uploaded files safely.
- `[DONE]` Add admin image library search/filter.
- `[DONE]` Add alt text fields for news/project main images and news galleries.
- `[DONE]` Add per-media captions for uploaded images.
- `[DONE]` Add image resizing/compression or document hosting-level image optimization.
- `[DONE]` Add max dimensions check to prevent very large images from exhausting memory.
- `[DONE]` Launch decision: keep videos YouTube-link based; revisit local video hosting only if explicitly needed.

## Security

- `[DONE]` State-changing admin forms use CSRF checks.
- `[DONE]` Admin login uses `password_verify`.
- `[DONE]` Output escaping uses `e()` / `htmlspecialchars`.
- `[DONE]` Upload handling validates file type/size.
- `[DONE]` Raw stack/database errors are not intentionally shown to users.
- `[TODO]` Change demo admin credentials before any public deployment. Hash generator is available at `SITE/scripts/generate-password-hash.php`.
- `[DONE]` Keep real production config in untracked `SITE/includes/config.php`.
- `[DONE]` Add rate limiting or throttling for admin login and contact form.
- `[DONE]` Add stronger spam protection for contact form with rate limiting and honeypot.
- `[DONE]` Add basic PHP security headers in public/admin layout bootstrap.
- `[DONE]` Add configurable session cookie name, HttpOnly, SameSite and HTTPS secure flag.
- `[DONE]` Runtime rate-limit cache is ignored by Git.
- `[DONE]` Run a final security review after MySQL wiring.
- `[WAITING]` Real production admin email/password must be chosen by the client before deployment.
- `[DONE]` Launch decision: do not add CAPTCHA now; revisit only if spam appears or the client requests it.

## Weather, Camera And External Integrations

- `[DONE]` Camera/weather UI and modals exist.
- `[DONE]` Camera/weather values are admin-editable as demo/config content.
- `[DONE]` Add camera stream/embed URL support in admin and homepage modal.
- `[WAITING]` Replace camera preview with real live stream/embed once the camera is purchased/installed.
- `[DONE]` Choose weather source/API and implement live weather fetch/cache.
- `[DONE]` Add graceful fallback when weather/camera provider is unavailable.
- `[WAITING]` Client must provide camera stream URL/provider.
- `[WAITING]` Client must approve weather provider/API key approach.
- `[DONE]` Launch decision: keep live weather server-side cached.
- `[DONE]` Launch decision: server-side weather cache protects provider details and reduces rate-limit pressure.

## Email And Notifications

- `[DONE]` Contact messages are stored and visible in admin.
- `[DONE]` Add optional email notification when a contact message is submitted.
- `[DONE]` Add admin setting for notification recipient email.
- `[DONE]` Add failure-safe behavior if email delivery fails but message storage succeeds.
- `[WAITING]` Client/host must provide SMTP credentials or email provider.
- `[DONE]` Launch decision: admin inbox is enough initially; optional email notifications remain available when SMTP/provider details exist.

## Frontend QA And Accessibility

- `[DONE]` Public route smoke checks passed in previous phases.
- `[DONE]` Production setup QA modes cover all major public routes and fail on unsuppressed PHP warnings/notices.
- `[DONE]` Search/filter/sort hooks verified.
- `[DONE]` Main JS syntax checks passed.
- `[DONE]` Fallback route/DOM responsive QA pass completed for public pages and admin login/settings.
- `[DONE]` Modal/form/filter/sort hooks checked in rendered PHP/HTML.
- `[DONE]` CSS responsive/focus/overflow rules reviewed.
- `[DONE]` Manual browser QA checklist exists at `docs/manual-qa-checklist.md`.
- `[TODO]` Manual visual QA in a real browser on desktop, tablet and mobile before launch.
- `[TODO]` Full keyboard navigation pass for header nav, modals, forms and admin actions in a real browser.
- `[TODO]` Screen reader label/heading pass.
- `[TODO]` Check all Georgian text visually for overflow on small screens.
- `[PROBLEM]` Automated Playwright/in-app browser/Node REPL QA is blocked in this environment by local `EPERM` filesystem permission errors.
- `[DONE]` Launch decision: use `docs/manual-qa-checklist.md` or browser automation outside this sandbox while local Codex browser QA is blocked.

## Deployment And Operations

- `[DONE]` GitHub repo stores source code.
- `[DONE]` GitHub Pages was intentionally not used because PHP will not run there.
- `[TODO]` Pick production hosting that supports PHP and MySQL.
- `[DONE]` Prepare production config instructions.
- `[DONE]` Prepare upload folder permissions instructions.
- `[DONE]` Prepare deployment checklist for clone/install/config/database/import/admin password.
- `[DONE]` Deployment docs reflect current MySQL runtime/admin-auth coverage.
- `[DONE]` Add backup plan for database and uploaded media.
- `[DONE]` Add rollback plan for code deploys.
- `[DONE]` Add setup script migration runner with dry-run mode for existing MySQL databases.
- `[WAITING]` Need hosting provider details.
- `[WAITING]` Need production domain/subdomain.
- `[DONE]` Launch decision: deploy manually first; add GitHub Actions/FTP/SSH only after hosting provider/access method is known.

## Documentation And Handoff

- `[DONE]` `docs/project-worklog.md` records completed phases and QA.
- `[DONE]` `docs/banza-site-prompts.md` stores the main project prompt.
- `[DONE]` `docs/security-review-phase48.md` records post-MySQL security review findings.
- `[DONE]` This checklist exists at `docs/project-checklist.md`.
- `[DONE]` Update README with current setup, admin login, dev server and production notes.
- `[DONE]` Add "continue in Codex" handoff section for workplace continuation.
- `[DONE]` Document how to migrate runtime uploads.
- `[DONE]` Document how to replace demo content safely.
- `[DONE]` Document final release checklist.
- `[DONE]` Document launch-scope decisions at `docs/launch-decisions.md`.
- `[DONE]` Launch decision: keep README primarily Georgian with English commands, paths and config identifiers.

## Recommended Remaining Phase Order

1. `[DONE]` Phase 12: README/setup/handoff cleanup so workplace continuation is frictionless.
2. `[DONE]` Phase 13: Decide storage direction: keep JSON for approval phase or wire MySQL now.
3. `[DONE]` Phase 14: Start incremental MySQL-backed CRUD and import script.
4. `[DONE]` Phase 15: Expand MySQL runtime repository slice while keeping JSON fallback.
5. `[DONE]` Phase 16: Production config, admin password change workflow, security hardening.
6. `[WAITING]` Phase 17: Replace demo content/assets with client-approved content.
7. `[DONE]` Phase 18: Real weather/camera integration foundation.
8. `[DONE]` Phase 19: Responsive/browser QA fallback pass.
9. `[DONE]` Phase 20: Hosting deployment prep and final release checklist.
10. `[DONE]` Phase 21: Admin login and contact form rate limiting.
11. `[DONE]` Phase 22: Admin content/messages/trash search, filter and sort.
12. `[DONE]` Phase 23: Production setup script for initial admin and defaults.
13. `[DONE]` Phase 24: Admin media library search, filter and sort.
14. `[DONE]` Phase 25: Upload max-dimensions validation.
15. `[DONE]` Phase 26: Admin list preview links.
16. [DONE] Phase 27: Admin profile/password change flow.
17. [DONE] Phase 28: Admin bulk actions for content and messages.
18. [DONE] Phase 29: Admin image alt text fields.
19. [DONE] Phase 30: Public project detail page.
20. [DONE] Phase 31: News/projects load-more pagination.
21. [DONE] Phase 32: Homepage empty states for missing news/projects.
22. [DONE] Phase 33: Optional GD-based upload image optimization.
23. [DONE] Phase 34: Uploaded media alt/caption metadata.
24. [DONE] Phase 35: Content source/provenance metadata.
25. [DONE] Phase 36: Public route render smoke coverage.
26. [DONE] Phase 37: Launch content audit helper.
27. [DONE] Phase 38: Optional contact email notification foundation.
28. [DONE] Phase 39: Manual browser QA checklist.
29. [DONE] Phase 40: MySQL runtime settings reader.
30. [DONE] Phase 41: MySQL runtime pages/media reader.
31. [DONE] Phase 42: MySQL runtime news/projects reader.
32. [DONE] Phase 43: MySQL admin-save write-through sync.
33. [DONE] Phase 44: Admin save/sync failure visibility.
34. [DONE] Phase 45: Production setup migration runner.
35. [DONE] Phase 46: MySQL-backed admin authentication.
36. [DONE] Phase 47: Production auth/deployment note cleanup.
37. [DONE] Phase 48: Post-MySQL security review.
38. [DONE] Phase 49: Browser QA reattempt and handoff update.
39. [DONE] Phase 50: Launch readiness checker.
40. [DONE] Phase 51: Production MySQL smoke helper.
41. [DONE] Phase 52: Launch decision log.

## Definition Of Done For Production

- `[DONE]` All major public pages render in CLI smoke without unsuppressed PHP warnings/notices.
- `[DONE]` Admin can manage all launch-critical content.
- `[TODO]` Uploads, trash, contact messages and settings are verified after deployment.
- `[TODO]` Demo credentials are replaced with a generated password hash in MySQL `admins` table or untracked production config.
- `[TODO]` Real content, real links and real donation accounts are approved by the client.
- `[DONE]` Launch content audit command documents remaining client/content blockers.
- `[DONE]` Launch readiness checker reports admin coverage, content readiness and host/manual-QA waiting items.
- `[DONE]` Database and uploads have backup/restore instructions.
- `[TODO]` Mobile and desktop manual QA is complete.
- `[DONE]` Security review is complete after final storage/deployment decisions.
- `[TODO]` GitHub `main` contains final source and worklog/checklist are updated.
