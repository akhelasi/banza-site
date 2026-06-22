---
name: github-pages-manager
description: Manage GitHub repositories and GitHub Pages deployment for static websites, frontend projects, and web portfolios. Use when Codex is asked to create a new GitHub project, publish a site, fix GitHub Pages, split multiple sites into separate repositories, configure branches, add deploy workflows, or explain GitHub deployment steps.
---

# GitHub Pages Manager

Use this workflow when the task involves GitHub repositories or GitHub Pages.

## Defaults

- Prefer one repository per website unless the user explicitly wants a monorepo.
- For plain HTML/CSS/JS, publish from the `main` branch root or `/docs`.
- For build-based frontend projects, use GitHub Actions and deploy the built static output.
- Do not put multiple unrelated client sites inside one public repository unless there is a deliberate reason.
- Keep secrets in GitHub repository secrets or local environment variables, never in committed files.
- Remember that GitHub Pages does not run PHP or server-side code. PHP projects need a PHP host; GitHub can still store source code.

## Workflow

1. Inspect the project and decide whether it is static, build-based, or server-side.
2. If it is a new site, create or prepare a dedicated repository name.
3. Ensure the site has an entry file:
   - `index.html` for static sites.
   - built `index.html` at the artifact root for Actions deployments.
4. Choose deployment:
   - Static: Settings > Pages > Deploy from branch > `main` + `/` or `/docs`.
   - Build-based: Settings > Pages > Source > GitHub Actions, then add workflow.
5. Add `.nojekyll` when publishing raw static files that should bypass Jekyll processing.
6. Verify the published URL:
   - User/organization site: `https://USERNAME.github.io/`
   - Project site: `https://USERNAME.github.io/REPOSITORY/`
7. If assets break, check absolute paths, base paths, casing, and browser console errors.

## When Acting on GitHub

- Use GitHub MCP, `gh`, or the user's authenticated browser/session only when available.
- Ask for authorization if credentials or permissions are needed.
- Never ask the user to paste tokens into chat.
- Before deleting repositories, branches, releases, or Pages settings, ask for explicit confirmation.

## Final Response

State the repository, Pages source, published URL, verification performed, and any remaining manual setting the user must approve.
