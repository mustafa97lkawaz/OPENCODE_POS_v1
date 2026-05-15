# RUNSTEPS — How to run & test the POS

This project is now a **self-contained Electron desktop app**. It no longer
needs XAMPP or MySQL — it bundles its own PHP and uses SQLite.

Seeded login (all run modes):

```
Email:    samir.gamal77@yahoo.com
Password: 123456
```

---

## 0. One‑time setup (fresh clone / new machine only)

Skip this if you're on the machine where the work was done — everything below
is already in place there.

```powershell
# PHP deps
composer install

# Node deps (Electron, electron-builder, electron-updater, Laravel Mix)
npm install

# Env
copy .env.example .env        # then edit if needed
php artisan key:generate

# Database (SQLite — file is at database\database.sqlite)
php artisan migrate:fresh --seed
```

**Bundled PHP is NOT in git** (it's ~80 MB, gitignored). A fresh clone must
add it once: follow [electron/php/README.md](electron/php/README.md) — download
PHP 8.2 NTS x64, unzip into `electron/php/`, copy `php.ini-development` →
`php.ini`, enable the listed extensions. Verify:

```powershell
electron\php\php.exe -v        # should print PHP 8.2.x
```

---

## 1. Run the desktop app (primary way)

```powershell
npm run electron:start
```

What happens: Electron finds `electron\php\php.exe`, spawns it on
`127.0.0.1:8123` (or next free port up to 8200), waits until Laravel responds,
then opens the window. On first launch it copies the seeded DB to
`%APPDATA%\POS Desktop\database.sqlite` and creates the writable storage there.

- A queue worker (`php artisan queue:work`) is started automatically for printing.
- Logs: `%TEMP%\pos-desktop.log` (open it if the window misbehaves).

---

## 2. Run in a browser (quick web testing, no Electron)

Use the same bundled PHP server Electron uses:

```powershell
electron\php\php.exe -S 127.0.0.1:8123 -t public server.php
```

Then open <http://127.0.0.1:8123> and log in with the seeded account.
Press `Ctrl+C` to stop. (Plain `php artisan serve` also works but the bundled
PHP path mirrors production exactly.)

---

## 3. Run the test suite

```powershell
php artisan test
```

Expected: **11 passed** (SaleStore, BarcodeLookup, PrintController, +example).
Tests use an in-memory SQLite DB and never touch `database\database.sqlite`.

A `pre-commit` git hook runs this automatically and blocks the commit on
failure (`php artisan test --stop-on-failure`).

Run a single test class:

```powershell
php artisan test --filter=SaleStoreTest
```

---

## 4. Build the Windows installer

```powershell
npm run electron:build
```

Output: `dist\` — an NSIS installer (per-user, choose install dir, desktop +
start-menu shortcuts).

Notes:
- The build logs 2 `winCodeSign` macOS-symlink errors — **harmless noise**, the
  installer still builds.
- The DB and writable storage live in `%APPDATA%\POS Desktop`, so they
  **survive reinstall / auto-update** — make a sale, reinstall, the sale is
  still there.

Final pre-ship check (manual): install the built `.exe` on a clean Windows
machine that has **no XAMPP**, make a sale, reinstall, confirm data survives.

---

## 5. Common tasks

| Task | Command |
|------|---------|
| Reset DB to seed data | `php artisan migrate:fresh --seed` |
| Clear caches | `php artisan config:clear; php artisan view:clear; php artisan route:clear` |
| List routes | `php artisan route:list` |
| Rebuild front-end assets | `npm run dev` (or `npm run watch`) |
| Back up the live DB | Settings page → **نسخ احتياطي للقاعدة** (saves to `Documents\PosBackups\`) |
| Retry failed print jobs | `php artisan queue:retry all` |

---

## 6. Troubleshooting

**The `.exe` / `npm run electron:start` opens nothing and exits instantly.**
This machine has `ELECTRON_RUN_AS_NODE=1` set in the environment. Launched from
a shell that inherits it, Electron runs as plain Node and quits. `npm run
electron:start` already clears it; if launching the built `.exe` from a
terminal, clear it first: `set ELECTRON_RUN_AS_NODE=` (cmd) /
`$env:ELECTRON_RUN_AS_NODE=$null` (PowerShell). Double-clicking the installed
app is unaffected.

**Window is blank / "connection refused".** Bundled PHP didn't start. Check
`%TEMP%\pos-desktop.log` for `[PHP]` / `[PHP-err]` lines. Usually a missing
`electron\php\php.ini` or a disabled extension (see
[electron/php/README.md](electron/php/README.md)).

**Email features fail.** `.env` `MAIL_PASSWORD` is still the placeholder
`REPLACE_WITH_NEW_APP_PASSWORD`. Set a real Gmail App Password.

**Printing does nothing.** Set the exact Windows printer name in Settings, then
use **طباعة تجريبية**. Printing is queued — confirm the queue worker is running
(it starts automatically with `electron:start`; for browser mode run
`php artisan queue:work` yourself).

**Need MySQL again (legacy).** `.env.mysql.backup` and
`database\database.sqlite.backup-pre-m2` hold the pre-migration state.
