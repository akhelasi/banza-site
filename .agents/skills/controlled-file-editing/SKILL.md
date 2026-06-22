---
name: controlled-file-editing
description: Control file editing when patch operations are slow, risky, or repeatedly hanging. Use when Codex needs to update starter-pack documentation, create or edit Markdown guidance, perform multi-file edits, or recover from stalled apply_patch/file-edit attempts.
---

# Controlled File Editing

Use this skill when editing starter-pack files or when patch operations have been slow in this thread.

## Problem Pattern

In this workspace, `apply_patch` and some file writes have repeatedly stalled on Markdown edits. Also, `.agents` may be read-only in some sandbox contexts, so adding or editing skills can require escalated write approval.

## Rules

1. Prefer small one-file edits.
2. Keep each patch short when using `apply_patch`.
3. Do not batch unrelated file updates.
4. Verify after every edit with `Get-Content`, `Select-String`, or `Get-ChildItem`.
5. If an edit operation takes more than 60 seconds, stop and report status before continuing.
6. If `.agents` or `.codex` is read-only, request scoped escalation instead of retrying blindly.
7. Use fallback writes only for generated starter-pack documentation when patching itself is the blocker.
8. Never use fallback writes for secrets, credentials, destructive changes, or unclear edits.

## Verification

After edits, read the changed file, check expected headings, and confirm no token, password, or placeholder secret was written.
