# BUILD — Producing the Windows installer

How to build `POS Desktop Setup <version>.exe` to hand to a client.
(Client run instructions are in [RUNSTEPS.md](RUNSTEPS.md) — they never build.)

---

## Prerequisites (one time)

```powershell
composer install
npm install
```

Bundled PHP must be present (gitignored, ~80 MB). On a fresh clone, follow
[electron/php/README.md](electron/php/README.md): download PHP 8.2 NTS x64,
unzip into `electron\php\`, copy `php.ini-development` → `php.ini`, enable the
listed extensions. Verify:

```powershell
electron\php\php.exe -v        # PHP 8.2.x
```

The build icon `build\icon.png` (256×256) is committed — no action needed.

---

## Build

```powershell
npm run electron:build
```

Takes ~3–5 min. Output:

```
dist\POS Desktop Setup 1.0.0.exe      ← give THIS to the client (~228 MB)
dist\win-unpacked\                    ← unpacked app (for local testing)
```

The version number comes from `package.json` `"version"`. Bump it there
before building a new release.

---

## Expected noise (NOT errors)

The log prints, several times:

```
ERROR: Cannot create symbolic link ... winCodeSign\...\darwin\10.12\lib\*.dylib
```

These are macOS-only files in electron-builder's `winCodeSign` cache that
Windows can't symlink without admin. **Harmless** — the build is configured
with `signAndEditExecutable: false` so winCodeSign is not required. The
installer still builds. `npm run electron:build` finishing with exit code 0 and
a `.exe` in `dist\` = success.

---

## Smoke‑test the build before shipping

```powershell
# IMPORTANT: clear this or the exe exits instantly (see Troubleshooting)
$env:ELECTRON_RUN_AS_NODE=$null
& ".\dist\win-unpacked\POS Desktop.exe"
```

Window should open at the login screen. Or install the real thing:
run `dist\POS Desktop Setup 1.0.0.exe`, launch from the desktop shortcut,
log in, make a sale.

Final check (do once): install on a **clean Windows machine with no XAMPP**,
make a sale, reinstall the same version, confirm the sale survived (data lives
in `%APPDATA%`, not the install folder).

---

## Troubleshooting the build

**`NO INSTALLER` / build exit 1, log mentions `icons` / `EOF`.**
`build\icon.png` is missing or not ≥256×256. Regenerate a 256×256 PNG into
`build\icon.png` and rebuild.

**Build exit 1, only `winCodeSign ... cannot execute` errors.**
Ensure `package.json` → `build.win.signAndEditExecutable` is `false`
(it is, by default in this repo). Do not enable Windows code signing unless you
have a certificate and admin rights.

**`win-unpacked\POS Desktop.exe` opens then closes immediately when run from a
terminal.** This machine has `ELECTRON_RUN_AS_NODE=1` in the environment.
Clear it first: `$env:ELECTRON_RUN_AS_NODE=$null` (PowerShell) or
`set ELECTRON_RUN_AS_NODE=` (cmd). The installed app (double-click) is fine.

**Tests block the commit.** A `pre-commit` hook runs `php artisan test`.
Fix failing tests, or commit the build separately.

---

## Optional: auto‑update

`electron-updater` is wired but inert until you add a publish feed. To enable:
set `build.publish` to a GitHub releases provider in `package.json`, build,
and upload `dist\*.exe` + `*.blockmap` + `latest.yml` to a GitHub Release.
Clients then get update prompts on launch.
