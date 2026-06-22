# Tooling Stability Notes

This workspace has shown repeated slowdowns when using large `apply_patch` operations on Markdown files. Some sandbox contexts also expose `.agents` and `.codex` as read-only, so skill or config edits may require scoped escalation.

## Current Mitigation

- Prefer small one-file edits.
- Avoid large multi-file patches.
- Verify every edit immediately.
- If patching stalls for more than about 60 seconds, stop and report status.
- Use the `controlled-file-editing` skill for starter-pack documentation edits.
- Request scoped escalation when editing `.agents` or `.codex` is denied by sandbox permissions.

## Fallback Policy

A controlled write fallback may be used for generated starter-pack documentation when `apply_patch` itself is the blocker. Fallback writes must stay inside the workspace and must be verified by reading the file afterward.

Do not use fallback writes for secrets, credentials, destructive operations, or unclear user requests.
