# Launch Signoff Template

Updated: 2026-06-26

Copy this template outside the repository when filling real client, hosting or credential details. Do not commit secrets, passwords, private host credentials, database dumps or private contact/payment data.

## Project

- Project:
- Environment URL:
- Git commit:
- Signoff date:
- Prepared by:
- Reviewed by:

## Client Content Approval

- Georgian page copy approved: yes/no
- News items approved: yes/no
- Project items approved: yes/no
- Football content approved: yes/no
- Village facts approved: yes/no
- Photos/assets approved: yes/no
- Donation account display approved: yes/no
- Contact/social display approved: yes/no

Evidence location:

- Client intake document:
- Approval message/file:
- Notes:

## Production Hosting

- Hosting provider:
- Domain/subdomain:
- PHP version:
- MySQL/MariaDB version:
- Deployment method:
- Document root:
- Upload/storage writable paths checked: yes/no
- Backup method checked: yes/no

Evidence location:

- Host setup notes:
- Non-secret screenshots/logs:
- Notes:

## Production Credentials

Do not write passwords or secrets here.

- Production admin email verified: yes/no
- Demo login rejected: yes/no
- `SITE/includes/config.php` exists only on host/untracked: yes/no
- `content_storage.driver=mysql`: yes/no
- SMTP/provider configured, if required: yes/no/not required

Evidence location:

- Non-secret command output:
- Notes:

## Command Evidence

Paste or attach non-secret command output.

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

Results:

- Migration dry-run:
- Migration apply:
- Import dry-run:
- Import apply:
- MySQL smoke:
- Strict readiness:
- Content audit:
- Route smoke:

## Manual Browser QA

Use `docs/manual-qa-checklist.md`.

- Desktop browser/version:
- Tablet viewport/device:
- Mobile viewport/device:
- Public pages passed: yes/no
- Header/navigation passed: yes/no
- Search/filter/sort passed: yes/no
- Modals passed: yes/no
- Contact form passed: yes/no
- Admin login/profile passed: yes/no
- Admin content CRUD passed: yes/no
- Upload/media passed: yes/no
- Trash restore/permanent delete passed: yes/no
- Settings passed: yes/no
- Keyboard navigation passed: yes/no
- Georgian text overflow checked: yes/no

Issues found:

- Issue:
- Severity:
- Resolution:

## Backup And Rollback

- Database backup created: yes/no
- Uploads backup created: yes/no
- Previous good commit recorded: yes/no
- Rollback instructions reviewed: yes/no

Evidence location:

- Backup note:
- Rollback commit:
- Notes:

## Final Decision

- Ready for public launch: yes/no
- Deferred items:
- Approver name:
- Approval date:
- Signature/confirmation:
