# Manual QA Checklist

Use this checklist before public launch or after major UI/admin changes. Automated browser QA has been unreliable in the current Codex environment, so run these checks in a normal browser on the development machine or hosting preview.

## Setup

- Start the PHP server from `SITE/`.
- Open the public site and admin panel in Chrome or Edge.
- Test desktop at 1440px wide.
- Test tablet at 768px wide.
- Test mobile at 390px wide.

## Public Pages

- Home page renders without horizontal scroll.
- Header logo, navigation and social links stay visible and do not overlap.
- Mobile navigation and tap targets are usable.
- Hero camera modal opens and closes by button, overlay click and `Esc`.
- Weather modal opens and closes by button, overlay click and `Esc`.
- Donation modal opens and closes and account text is readable.
- News listing search/filter/sort works without page reload or scroll jump.
- Projects listing search/filter/sort works without page reload or scroll jump.
- News detail gallery opens image modal and does not crop the image.
- News detail video modal opens YouTube embeds safely.
- Project detail page renders from a project card.
- About, history, football and contact pages render their hero, content and source note.
- Contact form shows validation errors for empty/invalid fields.
- Contact form stores a valid message in admin inbox.

## Admin Pages

- `/admin` redirects to the real login page.
- Login works with development credentials.
- Logout ends the session.
- Dashboard counts render.
- News create/edit/delete/restore/permanent delete works.
- Project create/edit/delete/restore/permanent delete works.
- Static page editing works for about, history, football and contact.
- Media upload validates type, size and dimensions.
- Media alt/caption fields save and render in admin.
- Settings page saves social links, donation accounts, camera, weather and notification settings.
- Contact messages can be searched, filtered, sorted, marked read, soft-deleted, restored and permanently deleted.
- Admin profile password change works in dev config mode.

## Keyboard

- `Tab` order is logical from header to page content to footer.
- Focus ring is visible on links, buttons, inputs, selects and textareas.
- Modals close with `Esc`.
- Modal close buttons are keyboard reachable.
- Admin bulk action checkboxes and buttons are keyboard reachable.

## Text And Layout

- Georgian headings and labels do not overflow on mobile.
- Buttons do not wrap awkwardly or clip text.
- Cards in news/projects/sidebar remain aligned and readable.
- Admin tables remain usable on mobile with horizontal table scroll where needed.
- Empty states render when news/projects/messages are unavailable.

## Launch Content

- Run `php SITE\scripts\setup-production.php --audit-content`.
- The command must report zero blockers before public launch.
- Replace demo admin credentials in untracked production config.
- Replace demo contact, social and donation values.
- Replace unapproved images or confirm their usage rights.
