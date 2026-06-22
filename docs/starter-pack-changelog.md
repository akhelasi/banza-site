# Starter Pack Changelog

This file records what changed in this starter pack and why, so future Codex threads do not need the same context explained again.

## 2026-06-19 - Initial Web Development Starter Pack

### Added

- `AGENTS.md`
- `.codex/config.example.toml`
- `.agents/skills/web-project-workflow/SKILL.md`
- `.agents/skills/php-sql-safety-review/SKILL.md`
- `.agents/skills/frontend-browser-qa/SKILL.md`
- `docs/mcp-and-skills-research.md`
- `docs/setup-checklist.md`
- `templates/project-agents.md`
- `templates/task-brief.md`
- `README.md`

### Why

The user wants Codex to work better with HTML, CSS, JavaScript, PHP, SQL, and general website projects. The pack gives Codex durable project rules, reusable skills, setup notes, and MCP recommendations.

### Notes

- `AGENTS.md` is meant to be copied into web project roots.
- Skills are repo-scoped under `.agents/skills`.
- MCP config is an example only; real secrets must not be committed.
- The provided ChatGPT share link could not be reliably accessed through tooling, so the first version was based on the user's description plus current public docs.

## 2026-06-19 - GitHub and GitHub Pages Management

### Added

- `.agents/skills/github-pages-manager/SKILL.md`
- `docs/github-management.md`

### Updated

- `README.md`
- `docs/setup-checklist.md`
- `templates/project-agents.md`

### Why

The user explained that they have GitHub account `akhelasi` and an existing repo `akhelasi/Tracers`, where multiple sites were uploaded because GitHub Pages and new repo deployment were confusing. The starter pack now tells Codex to help manage GitHub repository organization, GitHub Pages setup, and safe migration away from one mixed repository when appropriate.

### Decisions

- Prefer one repository per separate website.
- Keep the existing `Tracers` site working until replacements are verified.
- Use GitHub Pages for static HTML/CSS/JS sites.
- Do not use GitHub Pages for PHP/SQL runtime because GitHub Pages does not execute server-side PHP or run databases.
- For PHP/SQL projects, use GitHub for source control and use PHP-capable hosting for public production deployment.

## 2026-06-19 - PHP/SQL Local Development With XAMPP

### Updated

- `docs/github-management.md`
- `docs/setup-checklist.md`
- `README.md`

### Why

The user noted that paid PHP hosting should wait until a site is ready. During development, PHP/SQL projects should run locally on the user's computer with XAMPP.

### Decisions

- During development, PHP/SQL sites should run locally with XAMPP: Apache + PHP + MySQL/MariaDB.
- GitHub remains the code history and backup.
- Public PHP hosting is only needed when the site is ready to publish.
- Typical local URL examples:
  - `http://localhost/project-name/`
  - `http://localhost:8080/`

### Workflow

```text
Edit code locally
        |
Test with XAMPP
        |
Commit and push to GitHub
        |
Deploy to PHP hosting only when ready for public release
```

## 2026-06-20 - GitHub CLI Access

### Changed

- Installed GitHub CLI with the official `GitHub.cli` winget package.
- Authenticated GitHub CLI as `akhelasi`.
- Verified read access to `https://github.com/akhelasi/Tracers`.

### Why

The user wants Codex to manage GitHub setup, repositories, GitHub Pages, and future project publishing instead of doing those steps manually.

### Notes

- GitHub CLI works in the real Windows user context.
- The sandbox context may report the keyring token as invalid, so Codex should run GitHub CLI actions with approval/escalated execution when needed.
- Current verified `Tracers` repository details: public repo, default branch `master`.

## 2026-06-20 - GitHub Pages Test Project

- Created local test project `outputs/codex-test-page`.
- Created public GitHub repo `https://github.com/akhelasi/codex-test-page`.
- Enabled GitHub Pages from `main` branch root.
- Verified live URL: `https://akhelasi.github.io/codex-test-page/`.

## 2026-06-20 - Starter Pack GitHub Repository

- Added `scripts/install-starter-pack.ps1`.
- Added `docs/clone-and-install.md`.
- Published clean starter pack repo: `https://github.com/akhelasi/codex-starter-pack`.
- Included `AGENTS.md`, `.agents`, `.codex`, `docs`, `templates`, and `scripts`.
- Excluded generated `outputs` test projects and local cache files.

## 2026-06-22 - Senior Codex Engineering Workflow

- Added `.agents/skills/senior-web-engineer/SKILL.md`.
- Added `.agents/skills/adversarial-code-review/SKILL.md`.
- Added `docs/senior-codex-engineering-playbook.md`.
- Adapted Karpathy/Claude Code agentic coding practices to Codex: explore, plan, implement, verify, review, and publish.
- Kept MCP recommendations conservative: official docs, Context7, Playwright/browser, GitHub, Chrome DevTools, and local/dev database only when needed.

## 2026-06-22 - Local MCP Configuration

- Added global MCP configs for OpenAI Docs, Context7, Playwright, Chrome DevTools, GitHub, and Fetch.
- Verified GitHub CLI remains authenticated as `akhelasi`.
- Noted that new MCP tools may require Codex restart/new thread before appearing in tool discovery.
- Did not install sensitive project-specific MCPs such as production database, filesystem, Figma, Sentry, Slack, Drive, or Notion.

## 2026-06-22 - Sensitive MCP Defaults In AGENTS

- Added `Sensitive MCP Defaults` to `AGENTS.md`.
- Recorded that production database, broad filesystem, Figma, Sentry, Slack, Drive, Notion, and similar private-workspace MCP servers should not be installed by default.
- These MCPs should be enabled only for project-specific needs with user approval and narrow/read-only scopes where possible.

## 2026-06-22 - Work Computer Setup Prompt

- Added a work-computer setup prompt to `docs/clone-and-install.md`.
- The prompt tells Codex how to clone `https://github.com/akhelasi/codex-starter-pack`, check Git/Node/GitHub CLI/Codex CLI, run the install script, and avoid sensitive MCP servers without approval.
