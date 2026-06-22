---
name: adversarial-code-review
description: Perform a second-opinion review of AI-generated or human-written code for bugs, security issues, overengineering, dead code, missing verification, and deployment risk. Use after substantial implementation, before GitHub push/deploy, or when the user asks for senior-level review.
---

# Adversarial Code Review

Use this skill to review work with a skeptical senior-engineer mindset.

## Review Priorities

1. Correctness bugs and broken user flows.
2. Security issues: SQL injection, XSS, auth, CSRF, secrets, unsafe file upload, command injection.
3. Data loss or destructive migration risk.
4. Missing or weak verification.
5. Overcomplicated code, dead code, duplicated logic, or unrelated refactors.
6. Frontend regressions: layout overflow, broken assets, inaccessible controls, console errors.
7. Deployment and GitHub Pages/PHP hosting mismatch.

## Method

- Read the diff or changed files first.
- Trace real user/data flows.
- Prefer concrete file/line findings over vague advice.
- Separate blocking issues from cleanup suggestions.
- Do not rewrite code unless the user asked for fixes.

## Output Shape

Lead with findings ordered by severity. If no issues are found, say so and name the remaining test gaps.
