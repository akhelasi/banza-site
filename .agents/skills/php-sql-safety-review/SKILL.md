---
name: php-sql-safety-review
description: Review or modify PHP and SQL code for security, correctness, data safety, and maintainability. Use when a task touches authentication, forms, sessions, database queries, migrations, user input, admin panels, file uploads, payments, or production data.
---

# PHP and SQL Safety Review

Use this skill when PHP or SQL risk matters.

## Review Checklist

- Validate all external input at the boundary.
- Use prepared statements for SQL values.
- Confirm authorization, not only authentication.
- Check CSRF protection for state-changing forms.
- Escape output with the correct context.
- Keep secrets outside the repository.
- Avoid exposing raw errors to users.
- Use transactions for multi-step writes.
- Treat schema changes and deletes as high risk.
- Avoid production database actions without explicit approval.

## Common Fix Patterns

- Convert interpolated SQL to prepared statements.
- Add server-side validation before database writes.
- Normalize IDs with integer casting or strict validation.
- Add ownership checks before update/delete actions.
- Replace raw error output with logging plus a user-safe message.

## Verification

- Run `php -l` on changed PHP files when possible.
- If SQL changed, explain how it should be tested against a local/dev database.
- If a destructive operation is requested, stop and ask for explicit confirmation.
