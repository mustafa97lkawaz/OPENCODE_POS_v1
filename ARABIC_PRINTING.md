# Arabic Thermal Printing — Setup Guide

## Why Arabic Prints as Chinese Without This Fix

The XP-80 (Xprinter) driver sets the printer's default code page to **Chinese (GBK)**.
When raw UTF-8 Arabic bytes are sent via ESC/POS `text()`, the printer interprets them
as Chinese characters.

**The fix:** Render every Arabic line as a **PNG bitmap image** using PHP GD + Windows
Arabic font (Tahoma), then send the image pixels via ESC/POS `bitImage()`. The printer
only receives black/white dots — the code page is irrelevant.

---

## Components of the Fix

| Component | Purpose |
|-----------|---------|
| `khaled.alshamaa/ar-php` | Reshapes Arabic letters to their connected forms (initial/medial/final) before rendering |
| PHP GD + FreeType | Renders the shaped Arabic text to a PNG image |
| Windows `Tahoma.ttf` | Arabic-capable font at `C:/Windows/Fonts/tahoma.ttf` |
| `EscposImage::bitImage()` | Sends bitmap image via ESC/POS (more compatible than `graphics()`) |
| **GdEscposImage.php patch** | Fixes PHP 8.0+ incompatibility (`GdImage` object vs old `resource`) |

---

## Step-by-Step Setup (Fresh Installation)

### 1. Install PHP packages

```bash
cd C:\xampp\htdocs\pos_opencodee
composer require mike42/escpos-php
composer require khaled.alshamaa/ar-php
```

### 2. Patch GdEscposImage for PHP 8.0+

Open:
```
vendor/mike42/escpos-php/src/Mike42/Escpos/GdEscposImage.php
```

Find line ~62:
```php
if (!is_resource($im)) {
```

Change to:
```php
if (!is_resource($im) && !($im instanceof \GdImage)) {
```

> **Why:** PHP 8.0+ changed GD functions to return `GdImage` objects instead of
> resources. Without this patch, `EscposImage::load()` throws "Failed to load image."

### 3. Configure printer name in settings

```bash
php artisan tinker --execute="DB::table('settings')->update(['printer_name' => 'XP-80']);"
```

Or go to **الإعدادات** in the app → set printer name → **حفظ الإعدادات**.

To find the exact Windows printer name:
```powershell
wmic printer get name
```

### 4. Restart Apache (clear PHP opcache)

In **XAMPP Control Panel**: click **Stop** then **Start** on Apache.

Or visit: `http://localhost/pos_opencodee/public/clear_cache.php`

---

## After `composer install` or `composer update`

Running `composer install` / `composer update` **overwrites** vendor files, so the
GdEscposImage patch is lost. Re-apply it every time:

```
vendor/mike42/escpos-php/src/Mike42/Escpos/GdEscposImage.php  line ~62
Change:  if (!is_resource($im)) {
To:      if (!is_resource($im) && !($im instanceof \GdImage)) {
```

---

## How the Code Works (`PrintController.php`)

```
Arabic string
    → reshapeArabic()        # ar-php: connect letters (مرحبا not م ر ح ب ا)
    → renderTextImage()      # GD: render to PNG using Tahoma font
    → EscposImage::load()    # escpos-php: convert PNG to ESC/POS raster data
    → $printer->bitImage()   # send bitmap to printer
    → sendFileToPrinter()    # PowerShell rawprint.ps1 → Windows Print API (RAW)
```

Key constants:
```php
const IMG_WIDTH_80MM = 576;   // XP-80 printable dots (72mm × 8 dots/mm)
const IMG_WIDTH_58MM = 384;   // 58mm printers
```

---

## Troubleshooting

| Error | Cause | Fix |
|-------|-------|-----|
| `Cannot open printer: XP-80` | Wrong printer name | Run `wmic printer get name`, update settings |
| `Failed to load image.` | GdEscposImage PHP 8 patch missing | Re-apply patch (Step 2 above) |
| `include FilePrintConnector.php failed` | mike42/escpos-php not installed | `composer require mike42/escpos-php` |
| Arabic prints as Chinese | Old code running (opcache) | Restart Apache or visit clear_cache.php |
| Letters not connected | ar-php not loaded | `composer require khaled.alshamaa/ar-php` |
| `Class ArPHP\I18N\Arabic not found` | ar-php autoload issue | Add `require_once base_path('vendor/khaled.alshamaa/ar-php/src/Arabic.php');` |

---

## Required Windows Fonts

The following fonts are checked in order — at least one must exist:

```
C:/Windows/Fonts/tahoma.ttf     ← preferred
C:/Windows/Fonts/arial.ttf
C:/Windows/Fonts/calibri.ttf
```

All standard Windows 10/11 installations include these fonts.

---

## Printer Settings (Database)

| Column | Value | Notes |
|--------|-------|-------|
| `printer_name` | `XP-80` | Must match Windows Printers list exactly |
| `printer_type` | `80mm` | Use `58mm` for narrow paper |

Check current values:
```bash
php artisan tinker --execute="print_r(DB::table('settings')->first());"
```
