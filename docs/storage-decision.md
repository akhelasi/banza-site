# Storage Decision

Updated: 2026-06-24

## Decision

Use the current JSON content store only for development, demo, and content/design approval. Do not treat JSON storage as the production backend.

Production direction: move admin-managed content to MySQL before public launch.

## Why

JSON storage helped the project move quickly while the UI, content model, admin panel and trash behavior were still changing. It is simple to clone, inspect, edit and test in Codex.

For production, JSON is weak in the areas that matter most:

- concurrent admin edits can overwrite each other;
- backup and restore are less controlled than database backups;
- search/filter/pagination become awkward as content grows;
- audit and future reporting are harder;
- hosting providers usually expect PHP + MySQL for this type of site.

MySQL is already represented by `SITE/database/schema.sql`, so the next work should wire the existing behavior to that schema instead of expanding JSON further.

## Current JSON Scope

Keep JSON for now for:

- local development;
- Codex handoff;
- demo content;
- content/design review with the client;
- import source when moving to MySQL.

Do not add new production-only behavior that depends on JSON being permanent.

## Production MySQL Scope

The MySQL-backed implementation should cover:

- admins;
- pages;
- posts: news, projects and future team/football content if needed;
- media: uploaded images and YouTube links;
- settings: site, camera, weather and other key-value config;
- social links;
- donation accounts;
- contact messages;
- soft delete / restore / permanent delete.

## Migration Plan

1. Keep `SITE/storage/content.json` as the import source.
2. Add a repository layer under `SITE/includes/` or `SITE/includes/repositories/`.
3. Preserve the public variables currently exposed by `data.php` so public templates need minimal changes.
4. Add an import script that maps JSON to MySQL:
   - news/projects to `posts`;
   - static pages to `pages`;
   - galleries/videos to `media`;
   - settings/camera/weather to `settings`;
   - social links to `social_links`;
   - donation accounts to `donation_accounts`;
   - contact messages to `contact_messages`.
5. Run import only against a local/dev database first.
6. Keep uploads as filesystem runtime data under `SITE/uploads/`; import only their paths.
7. After MySQL reads/writes are verified, decide whether JSON fallback should remain for local demos.

## Repository Plan

Recommended files:

```text
SITE/includes/repositories/content-repository.php
SITE/includes/repositories/json-content-repository.php
SITE/includes/repositories/mysql-content-repository.php
SITE/scripts/import-json-to-mysql.php
```

Recommended approach:

- Start with a small interface-like function set rather than a large framework.
- Keep PDO prepared statements for all MySQL writes/reads.
- Keep output escaping in templates, not repositories.
- Keep CSRF/auth logic in admin pages.
- Keep upload validation in `uploads.php`.

## Risk Controls

- Do not run destructive SQL on a real database without explicit approval.
- Test import against local/dev MySQL only.
- Keep a copy of `content.json` before each import test.
- Keep runtime uploads backed up separately.
- Verify public pages and admin CRUD after switching reads/writes.
- Do not commit real database credentials.

## Phase 14 Recommendation

Implement MySQL in a controlled slice:

1. Add repository files and config flag for `json` vs `mysql`.
2. Keep JSON as default so the site still runs immediately after clone.
3. Implement MySQL reads for public content first.
4. Implement MySQL writes for one content type, preferably contact messages or projects.
5. Add the import script.
6. Expand to news/pages/settings/media after the first slice is verified.

This reduces risk compared with rewriting all CRUD at once.
