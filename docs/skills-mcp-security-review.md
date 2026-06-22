# Skills and MCP Security Review

Verified: 2026-06-19.

## Bottom Line

Do not install every popular skill or MCP server before a project starts. Use a small trusted set, keep most MCP servers disabled until needed, and prefer prompt/read-only approvals for anything that can access accounts, files, browsers, or databases.

No web review can prove a package is malware-free forever. Treat MCP servers and public skills like developer dependencies: useful, but part of the supply chain.

## Recommended Before Starting Web Projects

Local skills from this starter pack:

- `web-project-workflow`
- `php-sql-safety-review`
- `frontend-browser-qa`
- `github-pages-manager`

Bundled/system skills already present in Codex:

- `openai-docs`
- `skill-creator`
- `skill-installer`
- `imagegen`

## MCP Recommendation Tiers

### Tier 1 - Good Defaults

- OpenAI Docs MCP: official docs, read-oriented.
- Context7 MCP: current library/framework docs. Useful for Laravel, React, Vue, Tailwind, etc.
- Playwright MCP: browser testing for UI changes.

Keep them disabled until needed, except docs MCP if you often ask current documentation questions.

### Tier 2 - Enable When Needed

- GitHub MCP: official GitHub server. Use read-only or narrow toolsets first.
- Chrome DevTools MCP: useful for performance, console, network, and Chrome inspection.
- Fetch MCP: useful for fetching web pages, but web content can contain prompt injection.
- Git MCP: often unnecessary because Codex already has shell/git access.

### Tier 3 - Sensitive

- Database MCP: only local/dev or read-only credentials. No production DB by default.
- Filesystem MCP: avoid broad paths. Codex already has workspace file access.
- Figma, Sentry, Slack, Drive, Notion: enable only when a project really uses them.

## Not Recommended By Default

- Unknown public MCP servers from random lists.
- Experimental or insiders modes.
- Production database MCP.
- Broad filesystem access.
- Multiple overlapping servers with the same powers.
- Any server that asks for admin-level credentials without a clear need.

## Safety Rules

- Prefer official maintainers and the official MCP Registry.
- Pin versions for important projects instead of always using `latest`.
- Use `default_tools_approval_mode = "prompt"` for risky tools.
- Use read-only modes where available.
- Keep tokens in environment variables.
- Never paste tokens into chat.
- Review tool permissions before enabling a new MCP server.

## Security Notes From Research

- Codex loads skills progressively: name/description first, full `SKILL.md` only when a skill is selected.
- Codex repo skills live under `.agents/skills`.
- MCP config uses `mcp_servers.<id>` entries in Codex config.
- GitHub MCP supports toolsets and read-only mode, which reduces permissions and context size.
- MCP reference servers are useful examples, but their own README says they are not production-ready by default.
- Security researchers have shown MCP tool poisoning risks, where malicious tool descriptions can influence an agent or exfiltrate data.

## Source Links

- OpenAI Codex skills: https://developers.openai.com/codex/skills
- OpenAI Codex MCP: https://developers.openai.com/codex/mcp
- OpenAI Codex config reference: https://developers.openai.com/codex/config-reference
- MCP intro: https://modelcontextprotocol.io/docs/getting-started/intro
- Official MCP Registry: https://registry.modelcontextprotocol.io/
- MCP reference servers: https://github.com/modelcontextprotocol/servers
- Context7: https://github.com/upstash/context7
- Playwright MCP: https://github.com/microsoft/playwright-mcp
- Chrome DevTools MCP: https://github.com/ChromeDevTools/chrome-devtools-mcp
- GitHub MCP Server: https://github.com/github/github-mcp-server
- Invariant Labs MCP tool poisoning: https://invariantlabs.ai/blog/mcp-security-notification-tool-poisoning-attacks
