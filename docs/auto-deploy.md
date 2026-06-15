# Auto-Deploy To WordPress (Git Updater)

Goal: edit code in this monorepo, and have the WordPress site offer the new
theme/plugin version automatically — no manual upload, no stale versions.

## Why this exists

Previously both `style.css` and `kindertoys-core.php` had `Update URI: false`,
which tells WordPress to **never check for updates**. That is why an installed
`0.1.0` copy never advanced even though the repo moved to `0.3.x`. Those headers
are now replaced with Git Updater headers so updates can flow.

## Why a mirror is needed

Git Updater expects **one plugin or theme per repository, at the repo root**.
This project is a monorepo (`themes/` + `plugins/`), so the workflow mirrors
each sub-project into its own dedicated repo, and Git Updater reads from those.

```
themes/kindertoys        ->  multidigitalltd/kindertoys         (theme repo)
plugins/kindertoys-core  ->  multidigitalltd/kindertoys-core    (plugin repo)
```

You keep editing only this monorepo. The mirror is automatic on every push to
`main`.

## One-time setup

1. **Create two empty repositories** under the org:
   - `multidigitalltd/kindertoys`
   - `multidigitalltd/kindertoys-core`
   (No README/license — they are force-pushed from the monorepo.)

2. **Create a deploy token.** A GitHub Personal Access Token with
   `Contents: write` on both target repos (fine-grained PAT scoped to the two
   repos is best). Add it to this monorepo as a secret named **`DEPLOY_TOKEN`**
   (Settings → Secrets and variables → Actions).

3. **Install Git Updater** on the WordPress site (https://git-updater.com):
   - Settings → Git Updater → add both repos.
   - For **private** repos, add a GitHub token under the GitHub tab.

## How it works

- On every push to `main`, `.github/workflows/deploy-mirror.yml` copies each
  sub-folder to its target repo and force-pushes `main`.
- The `Version:` header in the mirrored copy is set to
  `<major>.<minor>.<run_number>` — major/minor from the source header, the
  GitHub run number as the patch. So WordPress always sees a higher version
  and shows "update available", and bumping the source minor (e.g. `0.3.x` →
  `0.4.0`) cleanly moves the mirror to `0.4.<run>`.
- In WP Admin → Dashboard → Updates (or Plugins/Themes) the update appears and
  can be applied with one click. Enable auto-updates on the theme and plugin
  to make it fully hands-off.

## Header reference

The Git Updater headers live in the source files and travel to the mirrors:

- Theme — `themes/kindertoys/style.css`:
  ```
  Update URI: https://github.com/multidigitalltd/kindertoys
  GitHub Theme URI: multidigitalltd/kindertoys
  Primary Branch: main
  ```
- Plugin — `plugins/kindertoys-core/kindertoys-core.php`:
  ```
  Update URI: https://github.com/multidigitalltd/kindertoys-core
  GitHub Plugin URI: multidigitalltd/kindertoys-core
  Primary Branch: main
  ```

## Alternative: direct deploy (if you have server access)

If the host provides **SSH** or **FTP/SFTP**, a single workflow can rsync/FTP
the two folders straight into `wp-content/` with no mirror repos and no Git
Updater plugin. That is simpler operationally; the Git Updater route was chosen
here because it needs no server credentials in CI.
