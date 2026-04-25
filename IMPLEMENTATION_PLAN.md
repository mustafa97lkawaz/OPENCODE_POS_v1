# POS Desktop – Implementation Plan (Milestone Roadmap)

> Companion to [PROJECT_REVIEW.md](PROJECT_REVIEW.md). This file is a step-by-step execution plan, organized as milestones. Each milestone is self-contained: do one, verify it, commit, move on.

**Total estimated time:** ~10 working days (2 weeks).
**Approach:** Path A from the review — keep Laravel, drop XAMPP, switch to SQLite, bundle PHP into Electron.

---

## Legend
- 🔴 **Critical** — must do; ships broken without it.
- 🟡 **Important** — improves quality / safety.
- 🟢 **Polish** — nice to have.
- ⏱ Time estimate (1 dev, focused).
- 📁 Files touched.
- ✅ Verification step (how you know it worked).

---

# Milestone 1 — Lock down data integrity 🔴
**⏱ ~3 hours · Goal:** No more corrupt sales / oversold stock.

### Step 1.1 — Wrap `SaleController::store()` in a transaction with row locks
📁 `app/Http/Controllers/SaleController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'payment_method' => 'required|in:cash,card,split',
        'items_json'     => 'required|json',
    ], [
        'payment_method.required' => 'يرجي اختيار طريقة الدفع',
    ]);

    $items = json_decode($request->items_json, true) ?: [];
    if (empty($items)) {
        return response()->json(['success' => false, 'message' => 'السلة فارغة'], 422);
    }

    try {
        $sale = DB::transaction(function () use ($request, $items) {
            $productIds = collect($items)->pluck('product_id')->all();
            $products   = Products::whereIn('id', $productIds)
                              ->lockForUpdate()
                              ->get()
                              ->keyBy('id');

            // Server-side stock + price validation
            foreach ($items as $i) {
                $p = $products[$i['product_id']] ?? null;
                if (!$p) throw new \Exception("منتج غير موجود: {$i['product_id']}");
                if ($p->stock_qty < $i['qty']) {
                    throw new \Exception("المخزون غير كافٍ: {$p->Product_name}");
                }
            }

            $subtotal   = $request->subtotal;
            $tax_amount = $request->tax_amount ?? 0;
            $discount   = $request->discount ?? 0;
            $total      = $subtotal + $tax_amount - $discount;
            $cash       = $request->cash_amount ?? 0;
            $card       = $request->card_amount ?? 0;
            $paid       = $cash + $card;

            $sale = Sale::create([
                'invoice_number' => 'INV-' . date('YmdHis'),
                'customer_id'    => $request->customer_id,
                'subtotal'       => $subtotal,
                'tax_amount'     => $tax_amount,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'cash_amount'    => $cash,
                'card_amount'    => $card,
                'paid_amount'    => $paid,
                'change_due'     => max(0, $paid - $total),
                'Status'         => 'completed',
                'Created_by'     => Auth::user()->name,
            ]);

            foreach ($items as $i) {
                $p = $products[$i['product_id']];
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $p->id,
                    'qty'        => $i['qty'],
                    'unit_price' => $p->sell_price,           // server price, not client
                    'total'      => $i['qty'] * $p->sell_price,
                ]);
                $p->decrement('stock_qty', $i['qty']);
            }
            return $sale;
        });
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }

    if ($request->ajax()) {
        return response()->json(['success' => true, 'sale_id' => $sale->id]);
    }
    session()->flash('Add', 'تم اكمال البيع بنجاح');
    return redirect()->back();
}
```

### Step 1.2 — Same treatment for `destroy()`
Wrap stock-restore loop in `DB::transaction` + `lockForUpdate`.

### Step 1.3 — Remove the routing leak
📁 `routes/web.php` — last line:
```php
// DELETE THIS:
Route::get('/{page}', [AdminController::class, 'index']);
```
If `AdminController@index` is actually used for something, replace with explicit `Route::get('admin/{section}', ...)` and a `where` constraint.

### Step 1.4 — Rotate the leaked Gmail password
1. Log into Gmail → revoke the leaked App Password.
2. Generate a new one.
3. Update `.env` only (never `.env.example`).
4. Add to `.gitignore` if not already.

### ✅ Verification
- Make a sale with 1 product. Stock should drop by qty.
- Open MySQL workbench, `SELECT stock_qty FROM products WHERE id = X` matches expected.
- Try posting `items_json` with `qty` > stock via Postman → expect 422 error.
- Visit `http://localhost/pos_opencodee/public/random-string-123` → should 404 (not redirect via wildcard).

---

# Milestone 2 — Drop XAMPP, switch to SQLite 🔴
**⏱ ~1 day · Goal:** A user without XAMPP can still run the app.

### Step 2.1 — Add SQLite to .env profile
Create `.env.sqlite` (kept out of git):
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:/xampp/htdocs/pos_opencodee/database/pos.sqlite
```

Create the file:
```bash
touch database/pos.sqlite
php artisan migrate:fresh --seed --env=sqlite
```

### Step 2.2 — Audit migrations for MySQL-only syntax
Search and replace:
- `enum(...)` → `string(...)` with an `in:` validator on the model side.
- `unsignedBigInteger` is fine.
- `text` is fine.
- `decimal(10,2)` is fine.
- `index` is fine.

📁 `database/migrations/2026_03_23_193009_create_sales_table.php` line 25:
```php
// before:
$table->enum('payment_method', ['cash', 'card', 'split'])->default('cash');
// after:
$table->string('payment_method', 10)->default('cash');
```

### Step 2.3 — Switch primary `.env` to SQLite
Replace these lines:
```env
DB_CONNECTION=sqlite
DB_DATABASE=C:/xampp/htdocs/pos_opencodee/database/pos.sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_USERNAME=root
# DB_PASSWORD=
```

### Step 2.4 — Re-seed
```bash
php artisan migrate:fresh --seed
php artisan cache:clear && php artisan config:clear
```

### ✅ Verification
- Stop MySQL in XAMPP. App still works.
- Login with seeded admin → you can browse, sell, print.
- `database/pos.sqlite` is ~1 MB.

---

# Milestone 3 — Bundle PHP into Electron, kill XAMPP dependency 🔴
**⏱ ~1.5 days · Goal:** Single `.exe` installer; no XAMPP install required.

### Step 3.1 — Download portable PHP 8.2 NTS
1. Get [PHP 8.2 NTS Win64](https://windows.php.net/downloads/releases/) → unzip into `electron/php/`.
2. Required extensions in `electron/php/php.ini`:
   ```ini
   extension=gd
   extension=mbstring
   extension=pdo_sqlite
   extension=sqlite3
   extension=openssl
   extension=fileinfo
   extension=curl
   ```
3. Test: `electron\php\php.exe -v`

### Step 3.2 — Spawn PHP from Electron `main.js`
📁 `electron/main.js` — at top, before `createWindow()`:
```js
const { spawn } = require('child_process');
let phpServer = null;

function startPhpServer() {
    const phpExe   = path.join(__dirname, 'php', 'php.exe');
    const appRoot  = path.join(__dirname, '..');
    const port     = 8123;
    phpServer = spawn(phpExe, ['-S', `127.0.0.1:${port}`, '-t', 'public', 'server.php'], {
        cwd: appRoot,
        windowsHide: true,
    });
    phpServer.stdout.on('data', d => console.log('[PHP]', d.toString()));
    phpServer.stderr.on('data', d => console.error('[PHP-err]', d.toString()));
    return `http://127.0.0.1:${port}`;
}

app.whenReady().then(() => {
    const url = startPhpServer();
    setTimeout(() => createWindow(url), 1500);  // give PHP a moment
});

app.on('before-quit', () => {
    if (phpServer) phpServer.kill();
});
```

### Step 3.3 — Update `createWindow()` to take URL
```js
function createWindow(loadUrl) {
    mainWindow = new BrowserWindow({...});
    mainWindow.loadURL(loadUrl);
    ...
}
```

### Step 3.4 — Update `.env` APP_URL
```env
APP_URL=http://127.0.0.1:8123
```

### Step 3.5 — Update `electron-builder` to include PHP + storage
📁 `package.json` `"build"` block:
```json
"files": [
  "electron/**/*",
  "app/**/*",
  "bootstrap/**/*",
  "config/**/*",
  "database/**/*",
  "public/**/*",
  "resources/**/*",
  "routes/**/*",
  "storage/**/*",
  "vendor/**/*",
  "artisan",
  "server.php",
  ".env"
],
"asarUnpack": [
  "electron/php/**/*"
]
```

### Step 3.6 — Bundle test
```bash
npm run electron:start          # without XAMPP running
npm run electron:build          # build .exe
```

### ✅ Verification
- Stop Apache + MySQL in XAMPP.
- Run `npm run electron:start` → window opens, login works, sales work, printing works.
- Move to a clean VM that has no XAMPP. Install built `.exe`. Same.

---

# Milestone 4 — Move printing off the request thread 🟡
**⏱ ~4 hours · Goal:** Slow printer never blocks the cashier.

### Step 4.1 — Set up database queue
📁 `.env`:
```env
QUEUE_CONNECTION=database
```
```bash
php artisan queue:table
php artisan migrate
```

### Step 4.2 — Create `PrintReceiptJob`
```bash
php artisan make:job PrintReceiptJob
```

📁 `app/Jobs/PrintReceiptJob.php`:
```php
public function __construct(public int $saleId) {}
public function handle(\App\Http\Controllers\PrintController $controller): void
{
    $controller->printReceipt($this->saleId);   // existing method
}
public $tries = 3;
public $backoff = 5;  // retry every 5 sec
```

### Step 4.3 — Dispatch from controller / sale flow
Replace any direct `printReceipt(...)` call after a sale with:
```php
\App\Jobs\PrintReceiptJob::dispatch($sale->id);
```

### Step 4.4 — Run worker from Electron
📁 `electron/main.js` — add alongside `startPhpServer`:
```js
let queueWorker = null;
function startQueueWorker() {
    const phpExe = path.join(__dirname, 'php', 'php.exe');
    queueWorker = spawn(phpExe, ['artisan', 'queue:work', '--sleep=2', '--tries=3'], {
        cwd: path.join(__dirname, '..'),
        windowsHide: true,
    });
}
// call startQueueWorker() right after startPhpServer()
// kill it in 'before-quit' alongside phpServer
```

### ✅ Verification
- Unplug printer. Make a sale. Cashier sees "تم البيع" instantly. Check `failed_jobs` table after retries.
- Plug printer back. Manually re-queue: `php artisan queue:retry all`.

---

# Milestone 5 — Refactor god view + extract POS JS 🟡
**⏱ ~1 day · Goal:** `pos.blade.php` becomes readable.

### Step 5.1 — Extract JS
1. Create `public/js/pos.js`.
2. Move everything inside `<script>...</script>` from `pos.blade.php` into it.
3. In the blade, add at end of `@section('js')`:
   ```blade
   <script>
   window.POS_ROUTES = {
       barcode:  '{{ url("pos/products/barcode") }}/',
       search:   '{{ route("pos.products.search") }}',
       store:    '{{ route("sales.store") }}',
       suspend:  '{{ route("sales.suspend") }}',
       printRcp: '{{ url("print/receipt") }}/',
   };
   </script>
   <script src="{{ asset('js/pos.js') }}?v={{ filemtime(public_path('js/pos.js')) }}"></script>
   ```
4. In `pos.js` use `POS_ROUTES.barcode + barcode.trim()` etc.

### Step 5.2 — Split blade into partials
Create:
- `resources/views/sales/partials/products-grid.blade.php` (the left products column)
- `resources/views/sales/partials/cart.blade.php` (the right cart column)
- `resources/views/sales/partials/payment-modal.blade.php`
- `resources/views/sales/partials/suspend-modal.blade.php`

Replace each block in `pos.blade.php` with `@include('sales.partials.cart')` etc.

### Step 5.3 — Replace `SaleController::show()` HTML string with a Blade partial
Create `resources/views/sales/partials/invoice-details.blade.php` with the same markup. Then:
```php
public function show($id) {
    $sale = Sale::with(['customer', 'saleItems.product'])->findOrFail($id);
    return view('sales.partials.invoice-details', compact('sale'))->render();
}
```

### ✅ Verification
- Diff: `pos.blade.php` should be < 250 lines.
- POS still works: scan, add, suspend, pay, print.
- Click any sale in the sales list → details modal renders correctly (no escaped Arabic).

---

# Milestone 6 — Validation & schema cleanup 🟡
**⏱ ~1 day · Goal:** Sane lengths, FormRequests, foreign keys.

### Step 6.1 — Generate FormRequest classes
```bash
php artisan make:request Sales/StoreSaleRequest
php artisan make:request Products/StoreProductRequest
php artisan make:request Products/UpdateProductRequest
php artisan make:request Customers/StoreCustomerRequest
php artisan make:request Expenses/StoreExpenseRequest
php artisan make:request StockAdjustments/StoreStockAdjustmentRequest
```

Move each `$request->validate([...])` block from controllers into its FormRequest's `rules()` and `messages()`.

Replace controller signatures: `public function store(StoreSaleRequest $request)` etc.

### Step 6.2 — Migration: shrink string lengths + add foreign keys
```bash
php artisan make:migration normalize_schema_lengths_and_fks
```

```php
public function up() {
    Schema::table('products', function (Blueprint $t) {
        $t->string('Product_name', 191)->change();
        $t->string('Created_by',   191)->change();
        $t->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
    });
    Schema::table('sale_items', function (Blueprint $t) {
        $t->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        $t->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
    });
    Schema::table('stock_adjustments', function (Blueprint $t) {
        $t->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
    Schema::table('expenses', function (Blueprint $t) {
        $t->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null');
    });
}
```
*(Note: SQLite doesn't support `->change()` — for SQLite, you may skip the length-shrink and only add FKs via `->foreign()`. The validation-side limit in FormRequests is what matters at runtime.)*

### Step 6.3 — Create `SuspendedSale` Eloquent model & swap raw queries
```bash
php artisan make:model SuspendedSale
```
Replace all `DB::table('suspended_sales')->...` calls in `SaleController` and `SuspendedSaleController` with `SuspendedSale::...`.

### Step 6.4 — Audit boolean checkbox forms
Search every blade for `<input type="checkbox" name="..."` — apply the same `<input type="hidden" value="0">` + checkbox `value="1"` pattern we used for `is_active`. Targets: `is_featured`, `is_variant`, `recurring`.

### ✅ Verification
- `php artisan route:list` is unchanged.
- Try to delete a category that has products → blocked by `restrict` FK.
- Try to delete a product that has been sold → blocked.
- Submit each form with checkboxes off — validation passes (sends `0`).

---

# Milestone 7 — Convert vendor patch to subclass 🟡
**⏱ ~30 min · Goal:** `composer install` no longer breaks Arabic printing.

### Step 7.1 — Create subclass in `app/Print/`
```php
<?php
namespace App\Print;

class SafeGdEscposImage extends \Mike42\Escpos\GdEscposImage
{
    public static function readImageFromGdResource($im) {
        if (!is_resource($im) && !($im instanceof \GdImage)) {
            throw new \Exception("Failed to load image.");
        }
        return parent::readImageFromGdResource($im);
    }
}
```

### Step 7.2 — Use it in `PrintController`
Replace `EscposImage::load($path, false)` with `\App\Print\SafeGdEscposImage::load($path, false)`.

### Step 7.3 — Revert vendor file
```bash
composer install   # restores the original
```
Verify printing still works.

### ✅ Verification
- `composer install` runs cleanly.
- Test print works.

---

# Milestone 8 — Theme cleanup, kill dead views 🟢
**⏱ ~1 hour · Goal:** Smaller installer, faster grep.

### Step 8.1 — List unused views
```bash
# from project root
ls resources/views/*.blade.php | xargs -n1 basename | sed 's/.blade.php//' \
  | while read v; do
      if ! grep -rq "view('$v\|view(\"$v\|@include('$v\|@include(\"$v" app/ resources/; then
        echo "UNUSED: $v"
      fi
    done
```

### Step 8.2 — Delete the unused ones
Likely candidates: `accordion`, `alerts`, `avatar`, `background`, `badge`, `blog`, `border`, `breadcrumbs`, `buttons`, `calendar`, `cards`, `carousel`, `chart-*`, `chat`, `collapse`, `contacts`, `counters`, `darggablecards`, `documentation`, `email`, `faq`, `gallery`, `icons`, `invoice` (if unused), `mail`, `mailbox`, `maps`, `modal`, `notifications`, `pagination`, `panels`, `pricing`, `profile`, `range`, `rating`, `tabs`, `tags`, `timeline`, `todo-tasks`, `tooltips`, `weather`, `widgets`, `wizards`.

```bash
git rm resources/views/{accordion,alerts,avatar,...}.blade.php
```

### ✅ Verification
- `php artisan route:list` succeeds.
- Open every link in the sidebar → no 404.
- App size drops by ~5 MB.

---

# Milestone 9 — Tests 🟡
**⏱ ~4 hours · Goal:** Catch regressions automatically.

### Step 9.1 — Test setup
📁 `phpunit.xml` — enable SQLite in-memory:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE"   value=":memory:"/>
```

### Step 9.2 — `tests/Feature/SaleStoreTest.php`
Cases:
- Sale with valid items → stock decrements, sale + items rows exist.
- Sale with `qty` > stock → 422, no rows written.
- Concurrent sales (two transactions) → second gets `lockForUpdate` retry, no oversell.

### Step 9.3 — `tests/Feature/BarcodeLookupTest.php`
- Existing barcode → returns product JSON.
- Variation barcode → returns parent product with variation.
- Unknown barcode → returns `success: false`.

### Step 9.4 — `tests/Feature/PrintControllerTest.php`
- `testPrint` returns success when `printer_name` matches an installed printer (skip on CI).
- `getPrinters` returns array.

### ✅ Verification
- `php artisan test` passes.
- Add a CI hook (`.git/hooks/pre-commit`):
  ```bash
  #!/bin/sh
  php artisan test --stop-on-failure
  ```

---

# Milestone 10 — Distribution polish 🟢
**⏱ ~4 hours · Goal:** Real installer + auto-update.

### Step 10.1 — Switch electron-builder target to NSIS installer
📁 `package.json`:
```json
"win": {
  "target": ["nsis"],
  "icon": "public/favicon.ico"
},
"nsis": {
  "oneClick": false,
  "perMachine": false,
  "allowToChangeInstallationDirectory": true,
  "createDesktopShortcut": true
}
```

### Step 10.2 — Move SQLite DB to user's AppData on first launch
📁 `electron/main.js`:
```js
const { app } = require('electron');
const userDb = path.join(app.getPath('userData'), 'pos.sqlite');
if (!fs.existsSync(userDb)) {
    fs.copyFileSync(path.join(__dirname, '..', 'database', 'pos.sqlite'), userDb);
}
process.env.DB_DATABASE = userDb;   // Laravel reads it
```
*(Or write a tiny bootstrap script that updates `.env` before spawning PHP.)*

### Step 10.3 — Auto-update with `electron-updater`
```bash
npm i electron-updater
```
📁 `electron/main.js`:
```js
const { autoUpdater } = require('electron-updater');
app.whenReady().then(() => autoUpdater.checkForUpdatesAndNotify());
```
Add a GitHub release workflow (`.github/workflows/release.yml`).

### Step 10.4 — Backup button
Add a small modal in settings: "Backup database now" → copies the SQLite file to `Documents/PosBackups/pos-YYYYMMDD-HHmm.sqlite`.

### ✅ Verification
- Build installer. Install on a fresh Windows VM. Make a sale. Reinstall. Sale survives (DB lives in AppData).
- Push a v1.0.1 tag → user gets update prompt next launch.

---

## Suggested Branch & Commit Strategy

```
main                                       (always shippable)
├── milestone/m1-data-integrity            ── PR ── merge
├── milestone/m2-sqlite                     ── PR ── merge
├── milestone/m3-bundle-php-electron        ── PR ── merge
├── milestone/m4-print-queue                ── PR ── merge
├── milestone/m5-refactor-pos               ── PR ── merge
├── milestone/m6-validation-schema          ── PR ── merge
├── milestone/m7-vendor-patch               ── PR ── merge
├── milestone/m8-cleanup                    ── PR ── merge
├── milestone/m9-tests                      ── PR ── merge
└── milestone/m10-distribution              ── PR ── merge → tag v1.0.0
```

One milestone = one PR. Don't mix — easier to roll back one milestone if it breaks.

---

## Pre-flight Checklist (before you start)

- [ ] Backup current MySQL database: `mysqldump -u root MustafaPos > backup-before-refactor.sql`
- [ ] Tag current state: `git tag pre-refactor && git push --tags`
- [ ] Make sure all four big README files are committed: `CLAUDE.md`, `PROJECT_REVIEW.md`, `IMPLEMENTATION_PLAN.md`, `ARABIC_PRINTING.md`.
- [ ] Decide answers to PROJECT_REVIEW.md §7:
  - One terminal vs many? → If many, **skip M2** and keep MySQL.
  - Auto-update on/off? → Affects M10.
  - Cloud sync? → Defer to v2.

---

## After All Milestones — Post-Launch v1.1 Backlog

These didn't make v1; revisit when you have feedback:

- **Multi-tenant** — let one app handle multiple stores.
- **Receipt designer UI** — drag/drop layout for the 80mm template.
- **Customer loyalty / store credit** — extend the Customer model.
- **Hardware cash drawer** — open via ESC/POS `pulse()` after each sale.
- **Reports v2** — interactive charts (Chart.js → ApexCharts), date-range comparator.
- **Offline-first sync** — outbox table, sync to a central server for chains.

---

## Quick Reference — Commands You'll Run a Lot

```bash
# After any code change:
php artisan config:clear && php artisan cache:clear

# After migration changes:
php artisan migrate:fresh --seed

# Test:
php artisan test
php artisan test --filter=SaleStoreTest

# Build desktop:
npm run electron:start          # dev
npm run electron:build          # ship .exe to /dist

# Queue worker (dev only — Electron handles this in prod):
php artisan queue:work
```
