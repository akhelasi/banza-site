# Client Launch Intake

Updated: 2026-06-26

Use this checklist to collect the real information needed to replace demo content and move the Banza site toward launch.

## Required Before Public Launch

- Production hosting provider name and control panel/access method.
- Production domain or subdomain.
- Production MySQL database credentials, stored only in untracked `SITE/includes/config.php` on the host.
- Production admin name, email and strong password.
- Real donation account details.
- Real contact email, phone, address/notes and preferred contact labels.
- Real social network URLs.
- Client-approved Georgian copy for all main pages.
- Approved photos/assets or explicit approval to use current placeholders until replacement.
- Manual browser QA results for desktop, tablet and mobile.

## Admin And Credentials

Collect:

- Admin name:
- Admin email:
- Temporary strong password delivery method:
- Who is allowed to change admin credentials:

Notes:

- Do not commit real credentials.
- Generate a password hash with `php SITE/scripts/generate-password-hash.php "strong-password"` only when config-based credentials are needed.
- For MySQL production mode, create/update the admin row through `SITE/scripts/setup-production.php`.

## Donation Accounts

For each account collect:

- Bank name:
- Account number / IBAN:
- Account holder:
- Public note:
- Display order:

Approval:

- Confirm exact spelling in Georgian.
- Confirm whether all accounts should be public.

## Social Links

For each social link collect:

- Label:
- URL:
- Icon/short label:
- Display order:

Required initial links:

- Facebook:
- Instagram:
- YouTube:

## Contact Page

Collect:

- Public email:
- Public phone:
- Address or location note:
- Contact form notification recipient email:
- Whether email notifications are required for launch:
- SMTP/provider details, if notifications are required:

## Village Facts

Collect verified values:

- Population:
- Household count:
- Vineyard area:
- Elevation above sea level:
- Municipality/region wording:
- Source or approver for each value:

## Main Pages

For each page, provide final Georgian copy, source/approver and approved images.

Pages:

- Home hero title/subtitle/buttons:
- About:
- History:
- Football team:
- Projects intro:
- Contact intro:

For each page:

- Final title:
- Short description:
- Full text:
- Hero/main image:
- Image alt text:
- Source status: client approved
- Approver name/date:

## News Items

For each launch news item:

- Title:
- Slug in Latin letters:
- Category:
- Publish date:
- Short description:
- Full text:
- Main image:
- Main image alt text:
- Gallery images:
- Gallery alt/captions:
- YouTube video links, if any:
- Approver name/date:

## Projects

For each launch project:

- Title:
- Slug in Latin letters:
- Status:
- Category:
- Featured on home/sidebar: yes/no
- Short description:
- Full text:
- Main image:
- Image alt text:
- Approver name/date:

## Football Content

Collect:

- Team official name:
- Short history:
- Current description:
- Team logo/photo approval:
- Gallery photos:
- YouTube videos, if any:
- Whether future match/player sections are needed:

## Camera

Collect when available:

- Camera provider/model:
- Stream/embed URL:
- Public display title:
- Status text:
- Preview image:
- Whether the stream should be public 24/7:

## Weather

Current implementation uses server-side cached Open-Meteo-style weather settings.

Confirm:

- Exact latitude:
- Exact longitude:
- Nearby places to display:
- Cache interval preference:
- Whether a different provider/API key is required:

## Hosting And Deployment

Collect:

- PHP version:
- MySQL/MariaDB version:
- SSH/FTP/control panel access method:
- Document root path:
- Upload/storage writable path rules:
- Backup tools provided by host:
- Email sending support:

Before launch on the host:

```bash
php SITE/scripts/setup-production.php --migrate --dry-run
php SITE/scripts/setup-production.php --migrate
php SITE/scripts/import-json-to-mysql.php --dry-run --only=all
php SITE/scripts/import-json-to-mysql.php --only=all
php SITE/scripts/setup-production.php --email=admin@example.com --password-env=BANZA_ADMIN_PASSWORD --dry-run
php SITE/scripts/setup-production.php --email=admin@example.com --password-env=BANZA_ADMIN_PASSWORD
php SITE/scripts/check-mysql-smoke.php --admin-email=admin@example.com --strict
php SITE/scripts/check-launch-readiness.php --strict
php SITE/scripts/setup-production.php --audit-content
```

## Manual QA Signoff

Use `docs/manual-qa-checklist.md` and record:

- Desktop browser/version:
- Tablet viewport/device:
- Mobile viewport/device:
- Header/nav checked:
- Modals checked:
- Forms checked:
- Admin login checked:
- Admin create/edit/delete/trash checked:
- Georgian text overflow checked:
- Keyboard navigation checked:
- Signoff name/date: