# File Maintenance Guide

Use this guide when changing the Codex starter pack. The goal is that the user should not need to re-explain where information belongs.

## Core Rule

Update the smallest file that owns the information. Do not duplicate the same rule in many files unless it must be visible from multiple entry points.

## What To Change Where

### `AGENTS.md`

Change when:

- Codex needs durable behavior instructions for every copied project.
- A rule affects how Codex should work, verify, ask approval, or use skills/tools.
- A new starter-pack file or skill should be discoverable from the top-level map.

Do not put long explanations here. Link to `docs/` instead.

### `.agents/skills/*/SKILL.md`

Change when:

- A reusable workflow should trigger for a specific task type.
- Codex needs step-by-step behavior for a repeated job.
- A workflow is too specific for general `AGENTS.md`.

Keep skills concise. Add references in `docs/` for longer background.

### `.codex/config.example.toml`

Change when:

- Adding, removing, or changing recommended MCP server examples.
- Adjusting approval modes, timeouts, or safe defaults.

Never add real secrets, tokens, database URLs, or personal credentials.

### `docs/setup-checklist.md`

Change when:

- Project setup steps change.
- Local tools, XAMPP, GitHub, or verification setup changes.

### `docs/github-management.md`

Change when:

- GitHub, GitHub Pages, repo naming, branches, deploy, or XAMPP hosting decisions change.

### `docs/skills-mcp-security-review.md`

Change when:

- Adding a new skill/MCP recommendation.
- Removing a risky or outdated recommendation.
- Security guidance changes.

### `docs/mcp-and-skills-research.md`

Change when:

- New research sources are checked.
- A recommendation needs source links or reasoning.

### `docs/starter-pack-changelog.md`

Change after every meaningful starter-pack change:

- Added/removed files.
- Changed GitHub setup.
- Changed MCP/skill recommendations.
- Published or updated the GitHub starter-pack repo.

### `docs/clone-and-install.md`

Change when:

- Clone URL changes.
- Install script behavior changes.
- Recommended install command changes.

### `templates/`

Change when:

- The user needs better reusable prompts or project instruction templates.

### `scripts/`

Change when:

- A repeated setup task should be automated.
- Manual copy/install steps become error-prone.

Test scripts after changing them.

## What Can Be Deleted

Safe to delete from a copied project when not needed:

- `outputs/`
- old experiments
- copied cache files
- unused templates

Do not delete from the starter-pack source unless the user asks or the changelog explains why.
