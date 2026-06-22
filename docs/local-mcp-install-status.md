# Local MCP Install Status

Verified: 2026-06-22.

## Installed / Configured Globally

Added to `C:\Users\User\.codex\config.toml` with `codex mcp add`:

- `openaiDeveloperDocs` - `https://developers.openai.com/mcp`
- `context7` - `npx.cmd -y @upstash/context7-mcp`
- `playwright` - `npx.cmd -y @playwright/mcp@latest`
- `chrome_devtools` - `npx.cmd -y chrome-devtools-mcp@latest`
- `github` - `https://api.githubcopilot.com/mcp/`
- `fetch` - `npx.cmd -y @modelcontextprotocol/server-fetch`

Already present:

- `node_repl`
- bundled `browser` plugin
- bundled `chrome` plugin
- bundled `computer-use` plugin
- GitHub CLI authenticated as `akhelasi`

## Notes

- `npm.cmd` and `npx.cmd` work. `npm.ps1` and `npx.ps1` are blocked by PowerShell execution policy, so use `.cmd` commands in MCP config.
- `gh` works in the real Windows context. Sandbox may not read the Windows keyring, so GitHub CLI commands may require scoped escalation.
- New MCP servers may require restarting Codex or opening a new thread before their tools appear in `tool_search`.
- `uvx` is not installed, so Fetch MCP was configured with an npm package. Recheck this if Fetch MCP fails at runtime.
- Database, filesystem, Figma, Sentry, Slack, Drive, and Notion MCP servers were intentionally not installed yet.

## Security Position

Use these MCPs only when needed. For GitHub write actions, deploys, database changes, browser-profile access, or destructive operations, ask the user for approval first.
