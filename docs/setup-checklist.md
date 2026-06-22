# Setup Checklist

## Per Project

1. Copy `AGENTS.md` into the project root.
2. Copy `.agents/skills` into the project root if you want these workflows available inside that repository.
3. Add project-specific commands to `AGENTS.md`, for example:
   - `npm run build`
   - `npm test`
   - `composer test`
   - `php -l`
4. Add database safety notes:
   - local DB name
   - migration command
   - seed command
   - production DB warning

## Personal Codex Setup

1. Open or create `~/.codex/config.toml`.
2. Copy only the needed MCP entries from `.codex/config.example.toml`.
3. Enable one MCP server at a time.
4. Restart Codex after config changes.
5. Test each MCP with a tiny read-only prompt before using it on real work.

## Recommended Installation Order

1. OpenAI Docs MCP.
2. Context7.
3. Playwright MCP.
4. GitHub MCP.
5. Sentry/Figma/database MCP only when a project actually needs them.

## GitHub Pages Setup

1. Use one repository per website when possible.
2. For HTML/CSS/JS sites, publish from `main` branch `/` or `/docs`.
3. For frontend projects with a build step, publish with GitHub Actions.
4. Add `.nojekyll` for static output if Jekyll processing is not needed.
5. Verify the live URL and asset paths before moving or deleting the old site.
6. Do not expect PHP or SQL to run on GitHub Pages.

## PHP/SQL Local Development

1. Use XAMPP while the site is being built.
2. Keep the project in GitHub for version history and backup.
3. Test locally through `http://localhost/...`.
4. Export or migrate the local database carefully when needed.
5. Use paid PHP hosting only when the site is ready to publish.

## Tool Requirements

- Node.js 18+ for many `npx` MCP servers.
- `uv` / `uvx` for Python-based MCP servers such as Git or Fetch.
- Docker only if you choose Docker-based servers such as local GitHub MCP.
- Project-local PHP, Composer, Node, npm, and database tools for verification.

## Token Handling

- Store tokens in environment variables.
- Never commit `.env` or token-bearing config.
- Use least-privilege tokens.
- Rotate tokens if they were pasted into a prompt or config accidentally.
