# Phase 48 Security Review

Date: 2026-06-26

Scope: post-MySQL wiring review for authentication, SQL access, CSRF coverage, upload handling, contact spam controls, delete behavior and production deployment notes.

## Reviewed Areas

- Admin authentication and profile update flow.
- MySQL repositories for admins, contact messages, settings, pages, posts, media metadata and import helpers.
- State-changing public/admin forms and CSRF handling.
- Upload validation, image processing and permanent-delete behavior.
- Contact form rate limiting and honeypot behavior.
- Production setup, migration and deployment checklist notes.

## Findings

- No user-controlled SQL interpolation was found in the reviewed MySQL runtime paths.
- Dynamic SQL in contact-message reads is limited to fixed internal branches and does not include request input.
- State-changing admin forms include CSRF tokens and server-side verification.
- Public contact form includes CSRF verification, rate limiting and a hidden honeypot field.
- Uploads are restricted to image MIME types, checked with `getimagesize`, capped at 5MB and capped by dimensions before storage.
- Uploaded file deletion is constrained to files under the uploads root and only deletes unreferenced upload paths.
- Production admin auth now supports the MySQL `admins` table with config fallback.

## Remaining Launch Requirements

- Replace demo credentials before public deployment.
- Test MySQL login, profile password change, content sync, imports and migrations against the real host or a host-like dev database.
- Complete manual browser QA on desktop and mobile.
- Replace demo/client-unapproved content, social links, contact values and donation accounts.
- Confirm `SITE/includes/config.php`, storage files and upload folders are not publicly downloadable on the chosen host.

## Verification Commands

```bash
php -l SITE/includes/auth.php
php -l SITE/admin/profile.php
php SITE/scripts/import-json-to-mysql.php --dry-run --only=all
php SITE/scripts/setup-production.php --check-routes
php SITE/scripts/setup-production.php --audit-content --allow-open
node --check SITE/assets/js/main.js
git diff --check
```
