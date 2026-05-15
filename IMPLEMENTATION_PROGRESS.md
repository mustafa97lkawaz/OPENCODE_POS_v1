# Implementation Progress Tracker

> Live status of milestones from [IMPLEMENTATION_PLAN.md](IMPLEMENTATION_PLAN.md).
> Updated automatically as each milestone is completed.

---

## Status Legend
- ⬜ Not started
- 🔄 In progress
- ✅ Completed
- ⏭ Skipped (with reason)
- ❌ Blocked

---

## Milestones

| # | Milestone | Status | Completed On | Notes |
|---|---|---|---|---|
| M1 | Lock down data integrity | ✅ | 2026-04-25 | Password sanitized in `.env` + docs; user must replace with new App Password |
| M2 | Drop XAMPP, switch to SQLite | ✅ | 2026-04-25 | MySQL no longer needed; Apache still required until M3 |
| M3 | Bundle PHP into Electron | ✅ | 2026-04-25 | PHP 8.2.30 bundled; **packaged .exe verified end-to-end** (HTTP 200, full UI, no XAMPP/MySQL) |
| M4 | Move printing to queued job | ✅ | 2026-04-25 | DB queue + `PrintReceiptJob`; Electron runs `queue:work`; retry→failed_jobs verified |
| M5 | Refactor god view + extract POS JS | ⬜ | — | — |
| M6 | FormRequests + foreign keys + schema cleanup | ⬜ | — | — |
| M7 | Convert vendor patch to subclass | ⬜ | — | — |
| M8 | Delete dead theme views | ⬜ | — | — |
| M9 | Tests | ⬜ | — | — |
| M10 | Real installer + auto-update + backup | ⬜ | — | — |

---

## Detailed Step Checklist

### M1 — Lock down data integrity ✅
- [x] 1.1 Wrap `SaleController::store()` in transaction with row locks
- [x] 1.2 Same treatment for `destroy()`
- [x] 1.3 Remove `Route::get('/{page}', ...)` wildcard (and unused `AdminController` import)
- [x] 1.4 Sanitized leaked Gmail password from `.env` + docs. **User action remaining:** revoke old App Password at https://myaccount.google.com/apppasswords, generate new one, replace `REPLACE_WITH_NEW_APP_PASSWORD` placeholder in `.env`

### M2 — Drop XAMPP, switch to SQLite ✅
- [x] 2.1 SQLite already configured at `database/database.sqlite`; backed up to `database.sqlite.backup-pre-m2`
- [x] 2.2 Fixed 6 enum columns across 4 migration files (enum → string with length cap)
- [x] 2.3 Switched primary `.env` `DB_CONNECTION=sqlite`; MySQL config commented; full backup at `.env.mysql.backup`
- [x] 2.4 `php artisan migrate:fresh --seed` — 25 migrations + 7 seeders ran clean
- [x] 2.5 (bonus) Made `ExpenseController` date functions driver-portable (`MONTH()`/`WEEK()` → `strftime()` on SQLite)

### M3 — Bundle PHP into Electron ✅
- [x] 3.1 Downloaded PHP 8.2.30 NTS Win64 (~30 MB) → extracted into `electron/php/` (82 MB). `php.ini` configured with extensions: gd, mbstring, pdo_sqlite, sqlite3, openssl, fileinfo, curl, intl. `php -m` confirms all loaded.
- [x] 3.2 `electron/main.js` — `startPhpServer()` auto-detects bundled PHP, spawns it on free port 8123-8200, polls until ready, gracefully falls back to XAMPP URL if missing
- [x] 3.3 `createWindow(loadUrl)` now takes URL parameter
- [x] 3.4 `APP_URL` injected via spawn env (cleaner than editing `.env` — Laravel `env()` reads it dynamically)
- [x] 3.5 `package.json` `build`: `"asar": false` (all files real on disk so PHP can read them), bundles full Laravel app + vendor + bundled PHP; excludes the un-shippable `public/storage` symlink + stale cache dirs
- [x] 3.6 **Packaged `.exe` verified end-to-end:** built `dist/win-unpacked/POS Desktop.exe` (~640 MB), launched it, bundled PHP spawned on 127.0.0.1:8123, `GET /` → **HTTP 200 full login UI + assets**, no XAMPP/MySQL running. Clean-VM test still recommended before shipping.

**M3 issues found & fixed during verification:**
- `winCodeSign` 7z extraction logs 2 macOS-symlink errors on Windows — **non-fatal noise**, build completes fine.
- Packaged PHP couldn't read files inside `app.asar` → set `"asar": false`.
- `public/storage` is a symlink electron-builder can't recreate on Windows (EPERM) → excluded from build; `ensureStorageLink()` recreates it at launch as a **directory junction** (no admin needed).
- Laravel `View\Compiler` FatalError: writable dirs (`storage/framework/{views,cache,sessions}`, `storage/logs`, `bootstrap/cache`) didn't exist → `ensureWritableDirs()` mkdir's them at launch.
- Packaged `.env` has a hardcoded dev SQLite path → main.js now injects portable `DB_DATABASE` via spawn env. ⚠️ **Known follow-up for M10:** bundled DB lives under Program Files (read-only in real installs) — must relocate to `%APPDATA%` on first launch.
- File logger added (`%TEMP%\pos-desktop.log`) + `uncaughtException`/`unhandledRejection` handlers for production diagnostics.

> ⚠️ **Test-harness note:** `ELECTRON_RUN_AS_NODE=1` is set in this machine's shell env. Launching the `.exe` from a shell that inherits it makes Electron run as bare Node and exit instantly. Normal double-click / the `electron:start` npm script (which clears the var) are unaffected.

### M4 — Move printing to queued job ✅
- [x] 4.1 `.env` `QUEUE_CONNECTION=database`; `php artisan queue:table` → `2026_05_15_190322_create_jobs_table`; migrated (`jobs` + `failed_jobs` tables present)
- [x] 4.2 `app/Jobs/PrintReceiptJob.php` — `$tries=3`, `$backoff=5`, `$timeout=60`, `failed()` logs to Laravel log
- [x] 4.3 Refactored `PrintController`: new `renderSaleReceipt(int): void` does the work & **throws** on failure (so retries/failed_jobs work); `printReceipt()` HTTP action now `PrintReceiptJob::dispatch()` + returns instantly `{success, queued:true}`
- [x] 4.4 `electron/main.js` — `startQueueWorker()` spawns bundled-PHP `artisan queue:work` after server is up; killed in `before-quit`
- [x] 4.5 Verified: dispatch returns instantly (row in `jobs` immediately); worker processes; non-existent sale → 3 retries → lands in `failed_jobs` with real exception. Frontend unchanged (already fire-and-forget; queued message is more accurate).

**Why this matters most after M3:** the bundled PHP built-in server (M3) is **single-threaded** — a synchronous multi-second print would freeze the *entire* POS for all requests. Queuing makes the sale request return in ms.

### M5 — Refactor god view + extract POS JS ⬜
- [ ] 5.1 Extract JS to `public/js/pos.js`
- [ ] 5.2 Split blade into partials
- [ ] 5.3 Replace `SaleController::show()` HTML string with Blade partial

### M6 — Validation & schema cleanup ⬜
- [ ] 6.1 Generate FormRequest classes
- [ ] 6.2 Migration: shrink string lengths + add foreign keys
- [ ] 6.3 Create `SuspendedSale` Eloquent model
- [ ] 6.4 Audit boolean checkbox forms

### M7 — Convert vendor patch to subclass ⬜
- [ ] 7.1 Create `App\Print\SafeGdEscposImage` subclass
- [ ] 7.2 Use it in `PrintController`
- [ ] 7.3 Revert vendor file (composer install)

### M8 — Theme cleanup ⬜
- [ ] 8.1 List unused views
- [ ] 8.2 Delete unused views

### M9 — Tests ⬜
- [ ] 9.1 Test setup (in-memory SQLite)
- [ ] 9.2 `SaleStoreTest`
- [ ] 9.3 `BarcodeLookupTest`
- [ ] 9.4 `PrintControllerTest`

### M10 — Distribution polish ⬜
- [ ] 10.1 Switch electron-builder target to NSIS installer
- [ ] 10.2 Move SQLite DB to user's AppData on first launch ⚠️ **REQUIRED** — M3 build currently points `DB_DATABASE` at the install dir which is read-only under Program Files; copy seed DB to `%APPDATA%\POS Desktop\database.sqlite` on first run and point `DB_DATABASE` there. Same applies to `storage/` writable dirs.
- [ ] 10.3 Auto-update with `electron-updater`
- [ ] 10.4 Backup button

---

## Pre-flight Checklist (before starting M1)

- [ ] Backup current MySQL database: `mysqldump -u root MustafaPos > backup-before-refactor.sql`
- [ ] Tag current state: `git tag pre-refactor && git push --tags`
- [ ] Decisions made (from PROJECT_REVIEW.md §7):
  - One terminal vs many? → **TBD**
  - Auto-update on/off?     → **TBD**
  - Cloud sync?             → **TBD**

---

## Activity Log

| Date       | Milestone | Action                   |
|------------|-----------|--------------------------|
| 2026-04-25 | —         | Plan & tracker created   |
| 2026-04-25 | M1        | Wrapped `SaleController::store()` in transaction + lockForUpdate + server-side stock validation + server-side price |
| 2026-04-25 | M1        | Wrapped `SaleController::destroy()` in transaction + lockForUpdate (stock restock atomic) |
| 2026-04-25 | M1        | Removed `Route::get('/{page}', ...)` wildcard catch-all from `routes/web.php` |
| 2026-04-25 | M1        | Removed unused `use App\Http\Controllers\AdminController` import from `routes/web.php` |
| 2026-04-25 | M1        | Sanitized leaked `MAIL_PASSWORD` from `.env`, `PROJECT_REVIEW.md`, `IMPLEMENTATION_PLAN.md` (verified: `.env` was never committed to git, so no public leak) |
| 2026-04-25 | M1        | Smoke test: `php artisan route:list` passes (267 lines), `route:clear`/`config:clear` clean. **M1 complete.** |
| 2026-04-25 | M2        | Backed up `.env` → `.env.mysql.backup`, `database.sqlite` → `database.sqlite.backup-pre-m2` |
| 2026-04-25 | M2        | Replaced 6 enum columns with `string` in: `create_customers_table`, `create_stock_adjustments_table`, `create_sales_table`, `add_fields_to_expenses_table` |
| 2026-04-25 | M2        | Switched `.env` `DB_CONNECTION` from `mysql` → `sqlite`; commented MySQL block |
| 2026-04-25 | M2        | `php artisan migrate:fresh --seed --force` succeeded — 25 tables, 1 admin user, 16 products, 6 categories, 3 customers, 3 suppliers, 8 expense categories, 14 permissions, 5 roles |
| 2026-04-25 | M2        | Made `ExpenseController` MONTH()/WEEK() driver-portable using `DB::connection()->getDriverName()` switch to `strftime()` for sqlite |
| 2026-04-25 | M2        | Smoke test: `User::count()=1`, `Products::count()=16`, `DB::connection()->getDriverName()=sqlite`. **M2 complete.** |
| 2026-04-25 | M3        | `electron/main.js` rewritten: portable-PHP detection + spawn on free port 8123-8200 + readiness polling + graceful XAMPP fallback + cleanup on quit. `createWindow()` takes URL. APP_URL injected via spawn env. |
| 2026-04-25 | M3        | `package.json` electron-builder config: bundle full Laravel app + vendor + storage; `asarUnpack` for PHP, SQLite DB, storage, uploads. |
| 2026-04-25 | M3        | Created `electron/php/` directory with detailed README for the PHP download / php.ini setup. |
| 2026-04-25 | M3        | `.gitignore` updated: `electron/php/*` excluded except README. Bundle won't get committed. |
| 2026-04-25 | M3        | `launcher.js` updated: skip XAMPP entirely when bundled PHP exists; skip MySQL always (we're on SQLite). |
| 2026-04-25 | M3        | `node --check electron/main.js` passes; `package.json` JSON valid. |
| 2026-04-25 | M3        | Downloaded PHP 8.2.30 NTS Win64 from windows.php.net (31.8 MB), extracted into `electron/php/` (82 MB), copied `php.ini-development` → `php.ini`, enabled gd/mbstring/pdo_sqlite/sqlite3/openssl/fileinfo/curl/intl. |
| 2026-04-25 | M3        | Smoke test: bundled `php.exe -S 127.0.0.1:8123 -t public server.php` served Laravel login (HTTP 200) and `/home` redirect (HTTP 302). |
| 2026-04-25 | M3        | `npm run electron:build` packaged `dist/win-unpacked/POS Desktop.exe` (~677 MB). winCodeSign macOS-symlink errors confirmed non-fatal. |
| 2026-04-25 | M3        | Fixed packaging: `"asar": false` (PHP can't read inside asar); excluded `public/storage` symlink; added `ensureWritableDirs()` + `ensureStorageLink()` (junction) at launch; portable `DB_DATABASE` via spawn env; file logger + crash handlers. |
| 2026-04-25 | M3        | **Packaged `.exe` verified end-to-end:** clean rebuild, launched, `[dirs] created`, bundled PHP up, `resolvedUrl=http://127.0.0.1:8123`, window created, `GET /` → HTTP 200 full UI. No XAMPP/MySQL. **M3 complete.** |
| 2026-04-25 | M10 (note)| Bundled SQLite DB resolves under install dir (read-only in real installs). Must relocate to `%APPDATA%` on first launch — flagged in M10.2. |
| 2026-04-25 | M4        | `.env` `QUEUE_CONNECTION=database`; created + migrated `jobs` table; `failed_jobs` already present. |
| 2026-04-25 | M4        | Created `app/Jobs/PrintReceiptJob.php` (tries=3, backoff=5, timeout=60, failed() logging). |
| 2026-04-25 | M4        | Refactored `PrintController`: `renderSaleReceipt()` throws on failure; `printReceipt()` HTTP action dispatches the job & returns instantly. |
| 2026-04-25 | M4        | `electron/main.js`: `startQueueWorker()` spawns bundled-PHP `queue:work`; killed on quit. |
| 2026-04-25 | M4        | Verified: instant dispatch (row in `jobs`); worker drains; bad sale → 3 retries → `failed_jobs` w/ real exception. **M4 complete.** |
