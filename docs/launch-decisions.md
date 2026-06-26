# Launch Decisions

Updated: 2026-06-26

This file records launch-scope decisions that do not need more code before the client provides real content, hosting and QA results.

## Content Structure

- Football stays a static managed page for launch.
  - Reason: the current requirement is one team page with editable text/image content. A reusable team post type would add admin and database complexity before there is recurring team content.
  - Revisit when the client needs match reports, player profiles, fixtures or multiple team sections.

## Admin Model

- Use one production admin account for launch.
  - Reason: the project is small and the current admin panel has no role-sensitive workflows. Multiple users/roles would add account lifecycle, authorization and audit requirements.
  - Revisit when more than one real editor needs access.

## Media And Video

- Keep videos as YouTube links for launch.
  - Reason: local video hosting increases storage, bandwidth, transcoding and backup risk. YouTube embeds cover the current news-detail requirement.
  - Revisit only if the client explicitly needs private/local video hosting.

## Security And Spam

- Do not add CAPTCHA for launch.
  - Reason: CSRF, honeypot and rate limiting already exist, and CAPTCHA adds friction. Add CAPTCHA only if real spam appears or the client requests it.

## Weather And Camera

- Keep weather fetching server-side with cache.
  - Reason: it avoids exposing provider/API details in the browser and reduces rate-limit pressure.
- Camera integration remains configurable embed/stream URL until the client buys and installs the camera.

## Notifications

- Admin inbox is enough for launch if SMTP details are not available.
  - Reason: contact messages are stored even if email delivery fails, and email notification already exists as an optional setting.

## Deployment

- Start with manual deployment to PHP/MySQL hosting.
  - Reason: hosting provider and access method are not chosen yet. GitHub Actions/FTP/SSH automation should be added only after the host is known.

## Documentation Language

- Keep README primarily Georgian with English command names and technical identifiers.
  - Reason: the handoff user is Georgian-speaking, while commands, paths and config keys are clearer in English.

## UI Architecture

- Keep the current hand-built CSS for launch.
  - Reason: the UI is stable and scoped. Extract reusable CSS/components only if a later phase adds many more UI states or pages.

## Storage Architecture

- Keep JSON as the editing source with MySQL sync for the current handoff phase.
  - Reason: this preserves local approval workflow and already provides MySQL runtime paths. A fully MySQL-native admin CRUD rewrite should wait until production hosting and multi-admin needs are clear.

## Seed Content

- Internet-researched content remains draft seed content, not launch-approved copy.
  - Reason: public sources are useful for layout and review, but final Georgian text, stats, photos and donation/contact details must be approved by the client before launch.

## Migration Strategy

- Keep the MySQL migration incremental.
  - Reason: the project already has JSON approval storage, import tooling, MySQL runtime readers and smoke checks. A full rewrite before hosting is known would add unnecessary risk.

## Browser QA Strategy

- Use the manual browser QA checklist or run browser automation outside this Codex sandbox.
  - Reason: the current environment blocks Playwright/in-app browser/Node REPL automation with local permission errors.