# MCP and Skills Research for Web Development

Verified: 2026-06-19.

This note summarizes the most useful Codex skills and MCP servers for an HTML, CSS, JavaScript, PHP, and SQL web developer.

## Core Codex Surfaces

- `AGENTS.md`: best for durable repository instructions, coding conventions, verification commands, and project-specific safety rules.
- `.agents/skills`: best for reusable workflows that Codex should trigger when a matching task appears.
- `~/.codex/config.toml`: best for user-level MCP configuration and tool policies.
- Plugins: best when distributing skills plus MCP/app configuration as an installable package.
- MCP servers: best for live external context or actions such as docs, browser automation, GitHub, Figma, logs, or databases.

## Recommended Skills

Use these local repo skills from this starter pack:

- `web-project-workflow`: broad web project implementation flow.
- `php-sql-safety-review`: PHP, SQL, auth, form, and data-safety review.
- `frontend-browser-qa`: browser verification for frontend changes.

Curated/built-in skills worth using when available:

- `skill-creator`: create or update your own repeatable Codex workflow.
- `skill-installer`: install curated or GitHub-hosted skills.
- `openai-docs`: answer Codex/OpenAI/API questions from current official docs.
- Browser/control skills in Codex desktop: use for local page verification.

## Recommended MCP Servers

### Strong Default Candidates

- OpenAI Docs MCP: current OpenAI and Codex developer docs.
- Context7: current library/framework docs for tools like Laravel, Vue, React, Next.js, Tailwind, Supabase, etc.
- Playwright MCP: browser automation and interaction checks for frontend work.
- Chrome DevTools MCP: Chrome inspection, screenshots, console/performance style checks.
- GitHub MCP: issues, pull requests, repository browsing, Actions, and workflow information beyond local git.

### Situational Candidates

- Fetch MCP: fetch and convert web content when web access is allowed.
- Git MCP: useful for MCP-only clients; less necessary when Codex already has shell and git.
- PostgreSQL/SQLite MCP: useful for read-only dev database inspection. Avoid production access by default.
- Figma MCP: useful when frontend work starts from Figma designs.
- Sentry MCP: useful for production error analysis when Sentry is used.

## Security Defaults

- Enable new MCP servers one at a time.
- Keep `default_tools_approval_mode = "prompt"` for tools that write, browse private data, use browser sessions, or access GitHub/databases.
- Keep database MCP connections read-only whenever possible.
- Do not place real tokens in committed config files.
- Use environment variables for tokens.
- Avoid broad filesystem MCP servers unless the allowed path is tightly scoped.

## Source Notes

- OpenAI Codex docs describe skills as reusable workflows with `SKILL.md`, optional scripts/references/assets, progressive disclosure, and `.agents/skills` discovery.
- OpenAI Codex MCP docs show `mcp_servers.<id>` config entries and list useful MCPs including OpenAI Docs, Context7, Figma, Playwright, Chrome DevTools, Sentry, and GitHub.
- The official MCP docs define MCP as an open standard for connecting AI apps to external systems such as local files, databases, tools, and workflows.
- The `modelcontextprotocol/servers` repository contains reference implementations such as Fetch, Filesystem, Git, Memory, Sequential Thinking, and Time, and points to the official MCP Registry for server discovery.
- Context7 provides both CLI+Skills and MCP modes for up-to-date code documentation.
- Microsoft Playwright MCP provides browser automation using Playwright and documents Codex setup.
- GitHub MCP is GitHub's official server for repository, issue, pull request, workflow, and code context.

## Source Links

- OpenAI Codex skills: https://developers.openai.com/codex/skills
- OpenAI Codex MCP: https://developers.openai.com/codex/mcp
- OpenAI Codex config reference: https://developers.openai.com/codex/config-reference
- MCP introduction: https://modelcontextprotocol.io/docs/getting-started/intro
- Official MCP registry: https://registry.modelcontextprotocol.io/
- MCP reference servers: https://github.com/modelcontextprotocol/servers
- Context7: https://github.com/upstash/context7
- Playwright MCP: https://github.com/microsoft/playwright-mcp
- Chrome DevTools MCP: https://github.com/ChromeDevTools/chrome-devtools-mcp
- GitHub MCP Server: https://github.com/github/github-mcp-server

The provided ChatGPT share link was not accessible through the available tooling, so this pack uses the user's described goal plus current public documentation.
