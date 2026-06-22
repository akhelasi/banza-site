# Codex Web Development Guide

Use this repository as a Codex starter pack for HTML, CSS, JavaScript, PHP, and SQL website work.

## Starter Pack Purpose

This repository contains a Codex starter pack. These files are not the product or website being built; they are working tools, instructions, checklists, and reusable skills that help Codex build, review, test, publish, and maintain web projects.

When this pack is copied into a real project, treat these files as guidance unless the user explicitly asks to edit the starter pack itself.

## Starter Pack Map

- `AGENTS.md`: primary Codex instructions. Read this first.
- `.agents/skills/`: reusable Codex skills. Read a skill only when the current task matches it.
- `.agents/skills/web-project-workflow/SKILL.md`: general HTML, CSS, JavaScript, PHP, and SQL website work.
- `.agents/skills/senior-web-engineer/SKILL.md`: senior-level workflow for substantial web development tasks.
- `.agents/skills/php-sql-safety-review/SKILL.md`: PHP, SQL, forms, auth, sessions, database writes, migrations, or user input.
- `.agents/skills/adversarial-code-review/SKILL.md`: second-opinion review for bugs, security, dead code, and verification gaps.
- `.agents/skills/frontend-browser-qa/SKILL.md`: UI, layout, HTML, CSS, JavaScript interactions, responsive behavior, and browser QA.
- `.agents/skills/github-pages-manager/SKILL.md`: GitHub repositories, GitHub Pages, publishing, deploy workflows, or site migration.
- `.codex/config.example.toml`: example MCP configuration; not active unless copied into real Codex config.
- `docs/`: setup notes, GitHub/deploy guidance, MCP/security research, and starter-pack history.
- `docs/senior-codex-engineering-playbook.md`: source-backed senior workflow adapted from Codex and Claude Code practices.
- `templates/`: reusable project instruction and task brief templates.
- `outputs/`: generated experiments and test projects; do not copy into real projects unless requested.

Use only the document, skill, MCP server, or tool needed for the current task. Do not load every starter-pack file by default.


## Sensitive MCP Defaults

Do not install or enable production database MCP, broad filesystem MCP, Figma, Sentry, Slack, Drive, Notion, or similar private-workspace MCP servers by default. These are project-specific and sensitive.

Enable them only when the active project actually needs them, after checking `docs/skills-mcp-security-review.md` and asking the user for approval. Prefer local/dev/read-only credentials and narrow scopes.
## User Profile

- The user is a web developer working mainly with HTML, CSS, JavaScript, PHP, and SQL.
- Prefer practical implementation over long theory.
- Explain important tradeoffs briefly, especially around security, data loss, or browser behavior.
- If the user writes in Georgian, answer in Georgian unless code, command output, or exact technical terms are clearer in English.

## Working Style

- Inspect the project before editing. Use `rg` and `rg --files` for search.
- Preserve the existing framework, file layout, naming, formatting, and database style.
- Keep edits narrowly scoped to the task.
- Do not overwrite user changes or unrelated files.
- Do not add new dependencies unless they clearly reduce risk or match the project.
- Never commit secrets, tokens, database dumps, `.env`, or generated credentials.
- Before destructive database, filesystem, or git operations, ask for explicit approval.

## Web Frontend Rules

- Make the first screen the actual usable app or page, not a marketing placeholder.
- Keep responsive layouts stable on mobile and desktop.
- Avoid overlapping text, overflowing buttons, layout jumps, and tiny tap targets.
- Use semantic HTML, accessible labels, keyboard-friendly controls, and visible focus states.
- Use existing CSS conventions first. If none exist, prefer simple, maintainable CSS with clear spacing and states.
- For UI changes, verify in a browser when possible and check at least one desktop and one mobile viewport.

## JavaScript Rules

- Prefer small, named functions over large anonymous blocks.
- Avoid global variables unless the project already uses them.
- Validate user input on the client only for UX; keep server-side validation authoritative.
- Handle loading, empty, error, and success states for async behavior.
- Avoid silently swallowing errors.

## PHP Rules

- Prefer PDO or the project's existing database abstraction.
- Use prepared statements for all user-controlled SQL values.
- Escape output for HTML context with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` unless a templating layer already handles escaping.
- Validate and normalize input at the boundary.
- Keep authentication, authorization, CSRF, sessions, and redirects explicit.
- Do not display raw database or stack errors to end users.
- Run `php -l` on changed PHP files when possible.

## SQL Rules

- Treat schema and data changes as high risk.
- Prefer additive migrations and reversible changes.
- Use transactions for multi-step writes when the database supports them.
- Check indexes for new lookup-heavy queries.
- Avoid `SELECT *` in production code unless the local pattern clearly uses it.
- Never run destructive SQL against a real database without explicit approval.

## Verification

Choose the smallest meaningful checks available in the project:

- PHP syntax: `php -l path/to/file.php`
- Composer projects: `composer test`, `composer lint`, or project-specific scripts
- Node projects: `npm test`, `npm run lint`, `npm run build`
- Static HTML/CSS/JS: run or open the page and verify behavior manually
- SQL changes: run against a local/dev database only, or provide the exact SQL for review

If checks cannot be run, say exactly what was not run and why.



