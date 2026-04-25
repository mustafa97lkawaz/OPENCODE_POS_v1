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
| M3 | Bundle PHP into Electron | ⬜ | — | — |
| M4 | Move printing to queued job | ⬜ | — | — |
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

### M3 — Bundle PHP into Electron ⬜
- [ ] 3.1 Download portable PHP 8.2 NTS into `electron/php/`
- [ ] 3.2 Spawn PHP from Electron `main.js`
- [ ] 3.3 Update `createWindow()` to take URL
- [ ] 3.4 Update `.env` APP_URL to `127.0.0.1:8123`
- [ ] 3.5 Update `electron-builder` to include PHP + storage
- [ ] 3.6 Bundle test on clean VM

### M4 — Move printing to queued job ⬜
- [ ] 4.1 Set up database queue
- [ ] 4.2 Create `PrintReceiptJob`
- [ ] 4.3 Dispatch from controller / sale flow
- [ ] 4.4 Run worker from Electron

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
- [ ] 10.2 Move SQLite DB to user's AppData on first launch
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
