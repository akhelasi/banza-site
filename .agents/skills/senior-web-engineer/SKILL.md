---
name: senior-web-engineer
description: Work like a senior web engineer on HTML, CSS, JavaScript, PHP, SQL, GitHub, deployment, debugging, refactoring, and feature work. Use when a task is larger than a tiny edit, touches multiple files, needs architecture judgment, requires verification, or benefits from planning before implementation.
---

# Senior Web Engineer

Use this skill for serious web development work.

## Operating Mode

1. Explore first: inspect files, commands, app structure, and existing patterns.
2. Plan when the change is unclear, multi-file, risky, or user-facing.
3. Implement narrowly: preserve existing architecture and style.
4. Verify with the smallest meaningful checks.
5. Review the result like a second engineer before final response.

Skip a formal plan for tiny, obvious edits.

## Engineering Standards

- Prefer existing project patterns over new abstractions.
- Keep product code separate from starter-pack docs and tools.
- Avoid adding dependencies unless the value is clear.
- Handle loading, empty, error, and success states.
- For PHP, validate input, escape output, and use prepared SQL statements.
- For SQL, avoid destructive changes without approval and prefer reversible migrations.
- For frontend, verify responsive layout and interactive behavior in a browser when possible.
- For GitHub and deployment, preserve the live site until the replacement is verified.

## Verification Ladder

Choose checks based on the project:

- Static HTML/CSS/JS: browser load, console, responsive viewport, links/assets.
- PHP: `php -l` for changed files, local XAMPP run when available.
- Node frontend: `npm run lint`, `npm test`, `npm run build` when present.
- SQL: local/dev database only; explain destructive SQL before running it.
- GitHub Pages: verify Pages status and live URL HTTP 200.

## Use Tools Carefully

- Use Context7 or official docs when current framework/API behavior matters.
- Use Playwright/browser tools for UI behavior.
- Use GitHub CLI/MCP for repository operations.
- Use database tools only for local/dev/read-only access unless the user explicitly approves more.

## Final Response

Report: changed files, verification, remaining risks, and exact URLs if deployed.
