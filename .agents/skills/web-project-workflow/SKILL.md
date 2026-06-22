---
name: web-project-workflow
description: Plan, implement, and verify website changes in HTML, CSS, JavaScript, PHP, and SQL projects. Use when Codex is asked to build a page, fix a UI bug, add a PHP feature, wire forms, update database-backed behavior, or improve an existing website.
---

# Web Project Workflow

Use this workflow for practical website work.

## Workflow

1. Inspect the project structure with `rg --files`.
2. Identify the framework or pattern before editing.
3. Find the entry points: routes, templates, scripts, styles, database layer, and config examples.
4. Make the smallest implementation that satisfies the request.
5. Keep frontend, backend, and SQL changes consistent.
6. Verify changed PHP syntax with `php -l` when available.
7. Run existing project checks when they are discoverable.
8. For visible UI changes, verify in a browser when possible.

## Implementation Rules

- Preserve existing code style.
- Use semantic HTML and accessible labels.
- Keep CSS responsive and avoid layout shifts.
- Use prepared SQL statements for user-controlled values.
- Escape server-rendered output in the correct context.
- Include loading, empty, and error states for async UI.
- Do not add dependencies without a clear project-level reason.

## Final Response

Summarize changed files, verification performed, and any checks that could not run.
