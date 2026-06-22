# GitHub Management Plan

This file records how Codex should help with GitHub for this user.

## User Situation

The user has GitHub account `akhelasi` and an existing repository:

- https://github.com/akhelasi/Tracers

The user previously uploaded multiple sites into that one project because GitHub Pages and new project deployment were confusing. Codex should take responsibility for repository and deployment management when asked.

## Recommended Direction

- Keep `Tracers` working until replacements are verified.
- For each separate site, create a separate repository.
- Publish plain HTML/CSS/JS sites with GitHub Pages from `main` branch root or `/docs`.
- Publish build-based frontend projects with GitHub Actions.
- Do not use GitHub Pages for PHP runtime. For PHP, use GitHub for source control and deploy to a PHP-capable host.
- During development, run PHP/SQL projects locally with XAMPP instead of paying for hosting before the site is ready.

## Practical Rules for Codex

- Inspect the existing repository before changing it.
- Preserve the live site until a new URL is confirmed working.
- Avoid deleting or moving old files until the user approves.
- Create migration branches for restructuring.
- Use clear repository names, for example:
  - `tracers`
  - `portfolio`
  - `client-site-name`
  - `project-name`
- Add `README.md` with live URL and setup notes.
- Add `.nojekyll` for static Pages sites unless Jekyll is intended.
- Check that asset paths work under project URLs such as `/REPOSITORY/`.

## GitHub Pages Quick Paths

Static site from branch:

1. Repository > Settings > Pages.
2. Source: `Deploy from a branch`.
3. Branch: `main`.
4. Folder: `/` or `/docs`.
5. Save.

Build-based site:

1. Repository > Settings > Pages.
2. Source: `GitHub Actions`.
3. Add a workflow that builds the site.
4. Upload the output folder with `actions/upload-pages-artifact`.
5. Deploy with `actions/deploy-pages`.

## Important Limits

- GitHub Pages publishes public websites.
- GitHub Pages does not execute PHP, Ruby, Python, or server-side code.
- For PHP/SQL projects, use hosting with PHP and a database; GitHub can still manage code, history, issues, and releases.

## Local PHP Development

Use XAMPP while building PHP/SQL sites:

```text
GitHub = code history and backup
XAMPP = local live/dev server
PHP hosting = production deployment when the site is ready
```

Typical local URLs:

- `http://localhost/project-name/`
- `http://localhost:8080/`

Recommended workflow:

```text
Edit code locally
        |
Test with XAMPP
        |
Commit and push to GitHub
        |
Deploy to PHP hosting only when ready
```

## Source Links

- Create a GitHub Pages site: https://docs.github.com/en/pages/getting-started-with-github-pages/creating-a-github-pages-site
- Configure a publishing source: https://docs.github.com/en/pages/getting-started-with-github-pages/configuring-a-publishing-source-for-your-github-pages-site
- GitHub MCP Server: https://github.com/github/github-mcp-server
