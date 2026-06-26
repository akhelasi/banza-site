# Completion Evidence Matrix

Updated: 2026-06-26

Use this matrix before marking the Banza website project complete. A checklist item is complete only when the evidence below exists and has been reviewed.

## Evidence Rules

- Do not store real secrets in Git.
- Use screenshots, command output copies, signed notes, or issue links as evidence outside the repository when they include private data.
- Keep this repository limited to templates, public notes and non-secret status updates.
- Treat `WAITING` as not complete until the named evidence exists.

## Production Definition Of Done

| Requirement | Current Status | Evidence Needed | Verification Command Or Action | Where To Record Evidence |
| --- | --- | --- | --- | --- |
| All major public pages render without PHP warnings/notices | Done locally | Route smoke output for current commit | `php SITE/scripts/setup-production.php --check-routes` | `docs/project-worklog.md` phase entry |
| Admin can manage all launch-critical content | Done locally | Launch readiness checker admin coverage output | `php SITE/scripts/check-launch-readiness.php` | `docs/project-worklog.md` phase entry |
| Uploads, trash, contact messages and settings verified after deployment | Waiting | Host/manual QA note confirming upload, message, settings, soft delete, restore and permanent delete flows | Run `docs/manual-qa-checklist.md` on production/preview | External QA signoff and checklist update |
| Demo credentials replaced | Waiting | Production admin row/config exists with real email/password hash; demo login rejected | `php SITE/scripts/check-mysql-smoke.php --admin-email=REAL_ADMIN --strict`; manual login test | External deployment note; never commit credentials |
| Real content, links and donation accounts approved | Waiting | Filled client intake and zero launch content blockers | `php SITE/scripts/setup-production.php --audit-content` | `docs/client-launch-intake.md` copy outside repo or signed client note |
| Launch content audit documents remaining blockers | Done locally | Audit command output showing current blockers or zero blockers | `php SITE/scripts/setup-production.php --audit-content --allow-open` for handoff; without `--allow-open` for launch | `docs/project-worklog.md` or deployment note |
| Launch readiness checker covers admin/content/host/manual-QA waiting items | Done locally | Readiness output from current commit | `php SITE/scripts/check-launch-readiness.php` | `docs/project-worklog.md` phase entry |
| Database and uploads have backup/restore instructions | Done | Deployment checklist contains database/uploads backup and rollback plan | Review `docs/production-deployment-checklist.md` | This repository |
| Mobile and desktop manual QA complete | Waiting | Signed manual QA checklist with desktop/tablet/mobile browser details | Run `docs/manual-qa-checklist.md` | External QA signoff; optionally summarize non-secret result in worklog |
| Security review complete after final storage/deployment decisions | Partially done | Security review after production host/config and MySQL smoke pass | Review `docs/security-review-phase48.md`, rerun relevant checks on host | New worklog phase after hosting exists |
| GitHub `main` contains final source and worklog/checklist are updated | Waiting until launch | Clean worktree, pushed commit hash, checklist latest phase, no uncommitted source/docs changes | `git status --short --ignored`; `git log --oneline -5` | Final worklog/checklist phase |

## Required Final Command Set

Run after client content, production config and hosting are in place:

```bash
php SITE/scripts/setup-production.php --migrate --dry-run
php SITE/scripts/setup-production.php --migrate
php SITE/scripts/import-json-to-mysql.php --dry-run --only=all
php SITE/scripts/import-json-to-mysql.php --only=all
php SITE/scripts/check-mysql-smoke.php --admin-email=REAL_ADMIN_EMAIL --strict
php SITE/scripts/check-launch-readiness.php --strict
php SITE/scripts/setup-production.php --audit-content
php SITE/scripts/setup-production.php --check-routes
```

Also run a normal-browser manual QA pass using `docs/manual-qa-checklist.md`.

## Current Non-Code Blockers

- Client-approved Georgian content is not present yet.
- Real donation/contact/social values are not present yet.
- Production hosting/domain are not chosen yet.
- Production MySQL smoke has not run yet.
- Production admin credentials are not chosen yet.
- Manual browser QA has not been completed outside this sandbox.

## Completion Decision

The project can be marked complete only when:

1. `php SITE/scripts/check-launch-readiness.php --strict` exits `0`.
2. `php SITE/scripts/setup-production.php --audit-content` exits `0`.
3. `php SITE/scripts/check-mysql-smoke.php --admin-email=REAL_ADMIN_EMAIL --strict` exits `0` on the host/dev database.
4. Manual QA signoff exists for desktop, tablet and mobile.
5. Client content/signoff exists.
6. GitHub `main` is pushed and the worktree is clean except ignored runtime files.