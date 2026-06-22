# Senior Codex Engineering Playbook

Verified: 2026-06-22.

This guide adapts strong Claude Code and Karpathy-style agentic coding practices for Codex and this user's web-development stack: HTML, CSS, JavaScript, PHP, SQL, GitHub Pages, XAMPP, and PHP hosting.

## What Was Found

I did not find an official public Karpathy-maintained Codex skill/MCP pack. Public coverage of Karpathy's Claude Code workflow describes a shift toward mostly agent-driven coding, with the human writing precise English instructions, reviewing results, and using AI review to catch overcomplication and dead code.

Official Claude Code and Codex docs converge on the same practical pattern: give the agent durable project instructions, let it explore before coding, provide executable verification, use MCP/tools only when needed, and keep context under control.

## Codex Translation

For Codex, the equivalent setup is:

- `AGENTS.md` instead of `CLAUDE.md` for durable project instructions.
- `.agents/skills/*/SKILL.md` for reusable workflows.
- `.codex/config.toml` or global `~/.codex/config.toml` for MCP configuration.
- GitHub CLI for repository and GitHub Pages management.
- Browser/Playwright/Chrome tools for UI verification.
- XAMPP for local PHP/SQL development before paid hosting.

## Senior Workflow

Use this loop for non-trivial work:

1. Explore: read relevant files, commands, routes, templates, database layer, and project docs.
2. Ask or infer: clarify only when the unknown changes the implementation risk.
3. Plan: write a short implementation plan for multi-file, risky, or unfamiliar work.
4. Implement: keep changes narrow and follow existing patterns.
5. Verify: run tests, builds, syntax checks, browser checks, or local XAMPP checks.
6. Review: use a second-opinion/adversarial review before deploy or push.
7. Publish: use GitHub/GitHub Pages/PHP hosting only after verification.

## Recommended Skills

Keep these in the starter pack:

- `senior-web-engineer`: main workflow for serious web development tasks.
- `adversarial-code-review`: second-opinion review after implementation.
- `web-project-workflow`: general web task execution.
- `php-sql-safety-review`: PHP, SQL, auth, forms, sessions, data safety.
- `frontend-browser-qa`: browser and responsive UI QA.
- `github-pages-manager`: GitHub repo, Pages, deploy, migration.
- `controlled-file-editing`: prevents this starter pack's known patch/write hangs.

## Recommended MCP/Tools

Tier 1:

- OpenAI Docs MCP: official OpenAI/Codex docs.
- Context7: current library/framework docs.
- Playwright MCP or Codex browser: frontend QA and interaction checks.

Tier 2:

- GitHub MCP or GitHub CLI: repositories, PRs, issues, Actions, Pages.
- Chrome DevTools MCP: console/network/performance checks, with isolated profile preferred.
- Fetch MCP: read web pages when browsing is allowed.

Sensitive:

- Database MCP: only local/dev/read-only by default.
- Filesystem MCP: usually unnecessary because Codex already has workspace access.
- Figma/Sentry/Slack/Drive/Notion: enable only for projects that actually use them.

## Security Position

Do not install random public skills or MCP servers just because they are popular. MCP and skills are supply-chain surfaces. Prefer official repos, clear maintainers, read-only modes, approval prompts, narrow toolsets, and disabled-by-default configuration.

## Sources

- OpenAI Codex skills: https://developers.openai.com/codex/skills
- OpenAI Codex MCP: https://developers.openai.com/codex/mcp
- OpenAI Codex config reference: https://developers.openai.com/codex/config-reference
- Claude Code best practices: https://code.claude.com/docs/en/best-practices
- Claude Code memory/CLAUDE.md: https://code.claude.com/docs/en/memory
- Claude Code overview: https://code.claude.com/docs/en/overview
- Claude Code hooks: https://code.claude.com/docs/en/hooks
- Context7: https://github.com/upstash/context7
- Playwright MCP: https://github.com/microsoft/playwright-mcp
- Chrome DevTools MCP: https://github.com/ChromeDevTools/chrome-devtools-mcp
- GitHub MCP Server: https://github.com/github/github-mcp-server
- MCP reference servers: https://github.com/modelcontextprotocol/servers
- MCP tool poisoning: https://invariantlabs.ai/blog/mcp-security-notification-tool-poisoning-attacks
- Karpathy Claude Code coverage: https://www.businessinsider.com/andrej-karpathy-claude-code-manual-skills-atrophy-software-engineering-tesla-2026-1
