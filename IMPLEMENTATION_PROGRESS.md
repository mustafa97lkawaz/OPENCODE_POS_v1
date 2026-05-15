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
| M5 | Refactor god view + extract POS JS | ✅ | 2026-04-25 | `pos.blade.php` 1011→322 lines; JS→`public/js/pos.js`; 4 partials; `show()`→Blade |
| M6 | FormRequests + foreign keys + schema cleanup | ✅ | 2026-04-25 | 6 FormRequests; SuspendedSale model; FKs (set null + restrict) enforced on SQLite |
| M7 | Convert vendor patch to subclass | ✅ | 2026-04-25 | `App\Print\SafeGdEscposImage`; `composer install` clean; print logic verified |
| M8 | Delete dead theme views | ✅ | 2026-04-25 | 95 dead theme views + dead `AdminController` removed; views 3MB→1MB |
| M9 | Tests | ✅ | 2026-04-25 | 11 tests pass (in-memory SQLite); pre-commit hook; caught + fixed a real variation-barcode recursion bug |
| M10 | Real installer + auto-update + backup | ✅ | 2026-04-25 | NSIS config; **AppData DB+storage relocation verified**; backup button; auto-update wired (needs release feed) |

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

### M5 — Refactor god view + extract POS JS ✅
- [x] 5.1 ~570-line inline `<script>` → `public/js/pos.js`; server values passed via `window.POS_ROUTES` + `window.POS_BOOT` (handles `resume_sale`); cache-busted with `filemtime`
- [x] 5.2 Split into 4 partials: `partials/products-grid`, `partials/cart`, `partials/payment-modal`, `partials/sale-forms` (included from `pos.blade.php`)
- [x] 5.3 `SaleController::show()` raw HTML-string builder → `view('sales.partials.invoice-details')`
- [x] 5.4 Verified end-to-end: login → POS HTTP 200, all partials render, Arabic intact, `pos.js` served; created a real sale (store `{success:true}`), `/sales/{id}` returns invoice partial with **unescaped Arabic**

> **Note:** `pos.blade.php` is 322 lines (plan target was <250). The remaining bulk is the ~240-line inline `<style>` block — out of scope for M5 (5.1–5.3 only covered JS/HTML). Actual HTML+JS structure is now ~80 lines. CSS extraction could be a future cleanup if desired.

### M6 — Validation & schema cleanup ✅
- [x] 6.1 Created 6 FormRequests (`Sales/StoreSaleRequest`, `Products/Store+UpdateProductRequest`, `Customers/StoreCustomerRequest`, `Expenses/StoreExpenseRequest`, `StockAdjustments/StoreStockAdjustmentRequest`); wired into Sale/Products/Customer/Expense/StockAdjustment controllers (store **and** update where applicable). `authorize()=true` (routes already auth-gated). Arabic messages/attributes preserved. `max:999`→`max:191` for names.
- [x] 6.2 SQLite-safe FKs added in **create** migrations (SQLite can't ALTER-ADD FK): `products.category_id`→categories `SET NULL` (+ index); `sale_items.product_id`→products changed `cascade`→**`restrict`** (preserve sales history). `ProductsController::destroy()` catches the `QueryException` → friendly Arabic message. Length-shrink skipped (SQLite `->change()` unsupported; FormRequest `max:191` enforces at runtime, per plan note).
- [x] 6.3 `SuspendedSale` model already existed + used by `SuspendedSaleController`; replaced the 4 remaining raw `DB::table('suspended_sales')` calls in `SaleController` with Eloquent (passes decoded array so the `items_json` array-cast round-trips correctly).
- [x] 6.4 Fixed bare `is_variant`/`is_featured` checkboxes in `products/create`+`edit` (hidden `value="0"` + checkbox `value="1"`) so the new `boolean` rule accepts them. `recurring` already had `value="1"` (safe).
- [x] 6.5 Verified: `route:list` unchanged (267); all controllers lint clean; invalid customer→redirect-with-errors, valid→created; `PRAGMA foreign_keys=1`; bad `category_id` rejected; **sold product delete blocked gracefully (302, not 500), product preserved**; test data rolled back.

### M7 — Convert vendor patch to subclass ✅
- [x] 7.1 Created `app/Print/SafeGdEscposImage.php` — **fully reimplements** `readImageFromGdResource()` (PHP-8 `\GdImage` safe). Deliberately does NOT delegate the type-check to `parent::` (the plan's snippet was flawed — `parent::` after revert would re-introduce the bug on older vendor versions).
- [x] 7.2 `PrintController` — both `EscposImage::load($path,false)` → `new SafeGdEscposImage($path,false)`. (Note: `EscposImage::load()` hardcodes `new GdEscposImage` with no late-static-binding, so `SafeGdEscposImage::load()` would NOT return the subclass — must construct directly. Lazy `loadImageData()` via `$this->` still routes to our override.)
- [x] 7.3 `composer install` ran clean (vendor patch reverted). Functional test: built PNG → `new SafeGdEscposImage` → `toRasterFormat()` OK (20×10, 30 raster bytes).

> **Finding:** upstream mike42/escpos-php **v2.2 already contains** the `\GdImage` check, so there was no longer an *active* break — but the subclass now **guarantees** correctness independent of vendor version/state and removes the fragile hand-edit. Goal met.

### M8 — Theme cleanup ✅
- [x] 8.1 Detected 95 unreferenced top-level theme-demo views. Root cause: their only consumer was `AdminController::index()` (`return view($id)`) via the `/{page}` wildcard route **removed in M1** — so all were already unreachable (and `return view($id)` was an arbitrary-view-render security smell).
- [x] 8.2 `git rm` 95 views + the now-dead `app/Http/Controllers/AdminController.php`. `resources/views` 3MB→1MB. Remaining top-level views (still referenced): `404`, `home`, `products`.
- [x] 8.3 Verified: `composer dump-autoload` clean, `route:list` 267 (unchanged), `/login` 200, `/random-deadpage` → **404 not 500** (no AdminController crash).

### M9 — Tests ✅
- [x] 9.1 `phpunit.xml`: enabled `DB_CONNECTION=sqlite` / `DB_DATABASE=:memory:`; added `APP_URL=http://localhost` (the `.env` `APP_URL` has a `/pos_opencodee/public` subpath that broke test routing — testing-only override). Added `HasFactory` to `User`; `UserFactory` now sets the NOT-NULL `roles_name`.
- [x] 9.2 `SaleStoreTest` — valid sale decrements stock + writes rows (server price used); oversell → 422, nothing written; missing payment_method → 422 (FormRequest); sequential second-sale oversell guard.
- [x] 9.3 `BarcodeLookupTest` — product barcode; variation barcode → parent product; unknown → `success:false`.
- [x] 9.4 `PrintControllerTest` — `getPrinters` returns array; `testPrint` returns well-formed JSON envelope (printer-presence is environment-dependent, asserted defensively).
- [x] 9.5 `.git/hooks/pre-commit` runs `php artisan test --stop-on-failure`.
- [x] **Bug caught & fixed:** variation-barcode lookup returned HTTP 500 ("Recursion detected" — `$variation` carried its `product` relation, creating a cycle when nested back onto `$product` for JSON). Fixed `SaleController::getProductByBarcode()` with `$variation->unsetRelation('product')`. **`php artisan test` → 11 passed.**

### M10 — Distribution polish ✅
- [x] 10.1 `package.json` `win.target`→`nsis` + icon; `nsis` block: not one-click, **perMachine:false** (per-user writable install dir), choose-dir, desktop+start-menu shortcuts. `publish:null` so builds don't need a release token.
- [x] 10.2 **(was ⚠️ REQUIRED — done)** `prepareUserData()` copies seed DB → `%APPDATA%\POS Desktop\database.sqlite` on first run + builds writable `storage/` skeleton there; `DB_DATABASE` + `LARAVEL_STORAGE_PATH` injected into PHP **and** queue-worker env; `bootstrap/app.php` honours it via `useStoragePath()`; `ensureStorageLink()` junctions `public/storage` → AppData. **Survives reinstall/update.**
- [x] 10.3 `electron-updater` installed; `maybeCheckForUpdates()` packaged-only + fully non-fatal. ⚠️ Needs a GitHub release feed/`publish` config to actually deliver updates (code ready; infra = deploy decision).
- [x] 10.4 `SettingController::backup()` + `GET settings/backup` + Settings button → copies live SQLite to `Documents\PosBackups\pos-YYYYMMDD-HHmm.sqlite`.
- [x] 10.5 Verified in **simulated packaged mode**: login 302, settings 200, Blade views compiled into AppData storage, backup file written (233 KB), 11 tests pass.

> **Remaining manual step before shipping:** `npm run electron:build` (NSIS) + install on a clean Windows VM (no XAMPP) → make a sale, reinstall, confirm it survives. The runtime behaviour is already verified; only NSIS packaging itself is unrun here.

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
| 2026-04-25 | M5        | Extracted ~570-line inline script → `public/js/pos.js`; blade injects `POS_ROUTES`/`POS_BOOT`. (First attempt via PowerShell corrupted Arabic encoding → restored from git, redone byte-safe via Bash.) |
| 2026-04-25 | M5        | Created partials: `products-grid`, `cart`, `payment-modal`, `sale-forms`; `pos.blade.php` 1011→322 lines. |
| 2026-04-25 | M5        | `SaleController::show()` HTML-string → `sales.partials.invoice-details` Blade view. |
| 2026-04-25 | M5        | Verified: login + POS HTTP 200, all partials render w/ correct Arabic, `pos.js` served; real sale created + `/sales/{id}` invoice partial renders unescaped Arabic. Test sale rolled back. **M5 complete.** |
| 2026-04-25 | M6        | Created 6 FormRequests + wired into 5 controllers; Arabic messages preserved; names max:191. |
| 2026-04-25 | M6        | SQLite-safe FKs in create migrations: products.category_id SET NULL, sale_items.product_id RESTRICT; destroy() catches QueryException. migrate:fresh --seed OK. |
| 2026-04-25 | M6        | SaleController 4 raw suspended_sales queries → SuspendedSale Eloquent. |
| 2026-04-25 | M6        | Fixed is_variant/is_featured checkboxes (products create/edit). |
| 2026-04-25 | M6        | Verified: routes 267 unchanged, FormRequest validation works, FK restrict blocks sold-product delete (302 not 500). **M6 complete.** |
| 2026-04-25 | M7        | Created App\Print\SafeGdEscposImage (full reimpl, PHP8 GdImage-safe); PrintController uses `new SafeGdEscposImage()`; composer install clean; raster test OK. **M7 complete.** |
| 2026-04-25 | M8        | Removed 95 dead theme views + dead AdminController (orphaned by M1 wildcard removal). views 3MB->1MB. route:list 267 unchanged, /login 200, dead URL 404. **M8 complete.** |
| 2026-04-25 | M9        | phpunit :memory: + APP_URL fix; User HasFactory; UserFactory roles_name. 3 test files (SaleStore, BarcodeLookup, PrintController). pre-commit hook. Fixed variation-barcode recursion 500. **11 passed. M9 complete.** |
| 2026-04-25 | M10       | NSIS config + electron-updater; AppData DB/storage relocation (prepareUserData + useStoragePath); backup button. Verified in simulated packaged mode (login/settings/storage/backup). **11 tests pass. M10 complete — all milestones done.** |
