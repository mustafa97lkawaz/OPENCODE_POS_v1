# PRD.md - Enterprise POS System Specification

## Project Overview
- **Project Name**: Enterprise POS
- **Project Type**: Point of Sale System
- **Stack**: Laravel 8 + MySQL + Valexa Dashboard (Bootstrap 4 RTL)
- **Mode**: Fresh database, single branch

---

## 1. Hardware Integration & Peripherals

### Thermal Receipt Printing
- Direct printing support for 80mm and 58mm thermal printers
- Customizable receipt headers/footers (Logo, Address, VAT Number)
- Automatic "Pop Drawer" command sent to cash drawer via printer RJ11 port

### Barcode Scanner Compatibility
- "Always-on" focus for SKU input field in POS screen
- Support for 1D (EAN-13, UPC) and 2D (QR) scanners
- Automatic "Add to Cart" functionality upon successful scan

### Label/Barcode Generation
- Built-in SKU generator for products without barcodes
- Printable sticker sheets with Price, Name, and Barcode

### Customer-Facing Display
- Secondary output support to show "Total Payable" and "Change Due" to customer

---

## 2. Advanced POS Terminal Features

### Keyboard Hotkeys
- F2 = Pay
- F4 = Suspend
- Esc = Clear cart

### Suspended Sales (Hold)
- Save current cart (if customer forgets wallet)
- Serve next person in line
- Retrieve suspended sales

### Multiple Payment Splitting
- Single bill paid partially in Cash and partially by Card

### Quick Product Grid
- Visual category-based grid for items without barcodes
- Like loose produce or services

### Walk-in vs Account Sales
- Quick guest checkout (walk-in)
- "Debt/Credit" sale for regular clients

---

## 3. Stock & Inventory Intelligence

### Low Stock Alerts
- Dashboard widgets
- Email/SMS notifications when items hit "Reorder Point"

### Stock Adjustment
- Damaged Goods
- Expired Items
- Personal Use

### Multi-Location Sync (Future)
- Track stock across different warehouses/branches

### Weighted Average Costing (WAC)
- Automatic recalculation of profit margins based on changing purchase prices

---

## 4. Financial & Localized Features

### Currency & VAT Localization
- Support multiple tax rates (Inclusive vs Exclusive)
- Custom currency formatting

### Expense Management
- Category-wise tracking (Rent, Salary, Electricity)
- Calculate Net Profit instead of just Gross Sales

### Profit/Loss Reports
- Deep-dive analytics by brand or category

### Digital Invoicing
- Email or WhatsApp PDF invoice to customer

---

## 5. System Architecture

### Laravel 8 Logic
- Heavy use of Service Classes for discount/tax calculations
- Keep Controllers clean

### MySQL Optimization
- Indexing on: sku, barcode, transaction_date columns
- Support 100,000+ records

### Valex Customization
- Replace standard tables with DataTables.net
- Instant filtering of sales history

---

## Module Breakdown (12 Core Modules)

### 1. Categories (تصنيفات)
- id, name (999), Description, Status, Created_by
- Used for: Product categorization, Profit/Loss reports by category

### 2. Products (منتجات)
- id, name (999), category_id, sku (unique), barcode, photo
- cost_price, sell_price, tax_rate, reorder_point
- wac (weighted average cost), stock_qty
- Status, Created_by

### 3. Customers (عملاء)
- id, name (999), phone, email, address, type (walk-in/account)
- account_balance (for credit sales), Status, Created_by

### 4. Suppliers (موردين)
- id, name (999), phone, email, address, Created_by

### 5. ExpenseCategories (تصنيفات المصروفات)
- id, name (999), Description, Created_by
- Categories: Rent, Salary, Electricity, Supplies, Other

### 6. Expenses (مصروفات)
- id, name (999), amount, category_id, date, description, Created_by

### 7. Sales (مبيعات)
- id, invoice_number, customer_id (nullable for walk-in)
- subtotal, tax_amount, discount, total
- payment_method (cash/card/split), cash_amount, card_amount
- paid_amount, change_due, Status (completed/suspended)
- Created_by

### 8. SaleItems (عناصر البيع)
- id, sale_id, product_id, qty, unit_price, total

### 9. SuspendedSales (مبيعات معلقة)
- id, invoice_number, customer_id, items_json, total, Created_by

### 10. StockAdjustments (تعديل المخزون)
- id, product_id, qty_change, type (damaged/expired/added/removed)
- reason, Created_by

### 11. Settings (الاعدادات)
- id, printer_type (58mm/80mm), receipt_header, receipt_footer
- vat_number, currency_symbol, store_name

### 12. POS Terminal (شاشة البيع)
- Full-page view (not modal-based CRUD)
- Quick product grid with category tabs
- Cart sidebar with real-time total
- Hotkey support (F2, F4, Esc)
- Payment modal (cash/card/split)
- Receipt print functionality

---

## Database Creation Order

1. categories
2. suppliers
3. customers
4. products
5. expense_categories
6. expenses
7. sales
8. sale_items
9. suspended_sales
10. stock_adjustments
11. settings

---

## Naming Conventions (per Agent Rules)

| Item | Rule |
|------|------|
| Model | PascalCase singular: `Category`, `Product` |
| Controller | Model + Controller: `CategoryController` |
| Table | lowercase plural: `categories` |
| Arabic text columns | PascalCase: `Category_name`, `Due_date` |
| FK | snake_case: `category_id`, `parent_id` |
| Audit | `Created_by` (stores Auth::user()->name) |

---

## Validation & Flash Messages (Arabic)

- Validation: `required` with Arabic message
- Flash: `Add`, `edit`, `delete` with Arabic confirmation
- Delete/Update: id from hidden input in modal