<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>توثيق نظام نقاط البيع - POS System Documentation</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.8;
            color: #333;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        .section {
            background: white;
            padding: 30px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section h2 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .section h3 {
            color: #764ba2;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #e83e8c;
        }
        pre {
            background: #282c34;
            color: #abb2bf;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 15px 0;
        }
        pre code {
            background: none;
            color: inherit;
            padding: 0;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-right: 4px solid #667eea;
        }
        .card h4 {
            color: #333;
            margin-bottom: 10px;
        }
        .card ul {
            list-style: none;
            padding: 0;
        }
        .card li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .card li:last-child {
            border-bottom: none;
        }
        .feature-box {
            display: flex;
            align-items: center;
            background: #f0f7ff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .feature-icon {
            font-size: 2em;
            margin-left: 15px;
        }
        .mermaid {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: right;
            border: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        footer {
            text-align: center;
            padding: 20px;
            color: #666;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🏪 نظام نقاط البيع (POS)</h1>
            <p>توثيق تقني شامل - Technical Documentation</p>
            <p>الإصدار 1.0 - March 2026</p>
        </header>

        <!-- Table of Contents -->
        <div class="section">
            <h2>📋 المحتويات</h2>
            <ul>
                <li><a href="#overview">1. نظرة عامة على النظام</a></li>
                <li><a href="#database">2. هيكل قاعدة البيانات</a></li>
                <li><a href="#workflow">3. سير العمل</a></li>
                <li><a href="#controllers">4. هيكل التحكمات</a></li>
                <li><a href="#routes">5. المسارات (Routes)</a></li>
                <li><a href="#views">6. الواجهات (Views)</a></li>
                <li><a href="#pos">7. شاشة POS</a></li>
                <li><a href="#modify">8. كيفية التعديل والتطوير</a></li>
            </ul>
        </div>

        <!-- Overview -->
        <div class="section" id="overview">
            <h2>1. نظرة عامة على النظام</h2>
            
            <h3>وصف النظام</h3>
            <p>نظام نقاط البيع (POS) المتكامل هو نظام لإدارة العمليات التجارية يتضمن:</p>
            
            <div class="grid">
                <div class="card">
                    <h4>💰 المبيعات</h4>
                    <ul>
                        <li>شاشة POS تفاعلية</li>
                        <li>دعم طرق دفع متعددة</li>
                        <li>إدارة المبيعات المعلقة</li>
                    </ul>
                </div>
                <div class="card">
                    <h4>📦 المخزون</h4>
                    <ul>
                        <li>إدارة المنتجات</li>
                        <li>تصنيف المنتجات</li>
                        <li>تتبع المخزون</li>
                    </ul>
                </div>
                <div class="card">
                    <h4>👥 العملاء</h4>
                    <ul>
                        <li>إدارة العملاء</li>
                        <li>الموردين</li>
                        <li>التقارير</li>
                    </ul>
                </div>
                <div class="card">
                    <h4>💸 المصروفات</h4>
                    <ul>
                        <li>تصنيف المصروفات</li>
                        <li>تتبع المصروفات</li>
                        <li>التقارير المالية</li>
                    </ul>
                </div>
            </div>

            <h3>التقنيات المستخدمة</h3>
            <ul>
                <li><strong>Backend:</strong> Laravel 10.x</li>
                <li><strong>Frontend:</strong> Blade Templates + jQuery</li>
                <li><strong>Database:</strong> MySQL</li>
                <li><strong>Authentication:</strong> Laravel Breeze/Auth</li>
                <li><strong>UI Framework:</strong> Valex Admin Template</li>
            </ul>

            <h3>هيكل الملفات</h3>
            <pre><code>pos_opencodee/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # التحكمات الرئيسية
│   │   └── Middleware/     # وسيطAuthentication
├── database/
│   └── migrations/         # هجرة قاعدة البيانات
├── resources/
│   ├── views/             # واجهات المستخدم
│   │   ├── sales/        # شاشة POS والمبيعات
│   │   ├── products/     # إدارة المنتجات
│   │   ├── customers/    # إدارة العملاء
│   │   └── layouts/     # القوالب الرئيسية
│   └── assets/           # CSS, JS, Images
├── routes/
│   └── web.php           # مسارات التطبيق
└── public/               # الملفات العامة</code></pre>
        </div>

        <!-- Database -->
        <div class="section" id="database">
            <h2>2. هيكل قاعدة البيانات</h2>

            <h3>ER Diagram - مخطط العلاقات</h3>
            <div class="mermaid">
                erDiagram
                    USERS ||--o{ SALES : "creates"
                    CUSTOMERS ||--o{ SALES : "places"
                    SALES ||--|{ SALE_ITEMS : "contains"
                    PRODUCTS ||--o{ SALE_ITEMS : "sold_as"
                    PRODUCTS }o--|| CATEGORIES : "belongs_to"
                    PRODUCTS ||--o{ STOCK_ADJUSTMENTS : "adjusted_in"
                    CUSTOMERS ||--o{ SUSPENDED_SALES : "has"
                    
                    USERS {
                        bigint id PK
                        string name
                        string email
                        string password
                        string role
                    }
                    
                    PRODUCTS {
                        bigint id PK
                        string Product_name
                        bigint category_id FK
                        string sku
                        string barcode
                        decimal sell_price
                        integer stock_qty
                        string Status
                    }
                    
                    SALES {
                        bigint id PK
                        string invoice_number
                        bigint customer_id FK
                        decimal total
                        string payment_method
                        string Status
                        string Created_by
                    }
                    
                    SALE_ITEMS {
                        bigint id PK
                        bigint sale_id FK
                        bigint product_id FK
                        integer qty
                        decimal unit_price
                    }
                    
                    CATEGORIES {
                        bigint id PK
                        string Category_name
                    }
                    
                    CUSTOMERS {
                        bigint id PK
                        string Customer_name
                        string phone
                        string type
                        string Status
                    }
            </div>

            <h3>جداول قاعدة البيانات</h3>
            
            <table>
                <tr>
                    <th>الجدول</th>
                    <th>الوصف</th>
                    <th>الحقول الرئيسية</th>
                </tr>
                <tr>
                    <td><code>users</code></td>
                    <td>المستخدمين والصلاحيات</td>
                    <td>id, name, email, password, role</td>
                </tr>
                <tr>
                    <td><code>products</code></td>
                    <td>المنتجات</td>
                    <td>id, Product_name, category_id, sku, barcode, sell_price, stock_qty, Status</td>
                </tr>
                <tr>
                    <td><code>categories</code></td>
                    <td>تصنيفات المنتجات</td>
                    <td>id, Category_name, description</td>
                </tr>
                <tr>
                    <td><code>sales</code></td>
                    <td>المبيعات</td>
                    <td>id, invoice_number, customer_id, subtotal, tax_amount, discount, total, payment_method, Status</td>
                </tr>
                <tr>
                    <td><code>sale_items</code></td>
                    <td>بنود المبيعات</td>
                    <td>id, sale_id, product_id, qty, unit_price, total</td>
                </tr>
                <tr>
                    <td><code>customers</code></td>
                    <td>العملاء</td>
                    <td>id, Customer_name, phone, email, address, type, account_balance, Status</td>
                </tr>
                <tr>
                    <td><code>suppliers</code></td>
                    <td>الموردين</td>
                    <td>id, Supplier_name, phone, email, address, Status</td>
                </tr>
                <tr>
                    <td><code>expense_categories</code></td>
                    <td>تصنيفات المصروفات</td>
                    <td>id, name, description</td>
                </tr>
                <tr>
                    <td><code>expenses</code></td>
                    <td>المصروفات</td>
                    <td>id, expense_category_id, amount, description, date, Status</td>
                </tr>
                <tr>
                    <td><code>stock_adjustments</code></td>
                    <td>تعديلات المخزون</td>
                    <td>id, product_id, adjustment_type, quantity, reason, Created_by</td>
                </tr>
                <tr>
                    <td><code>suspended_sales</code></td>
                    <td>المبيعات المعلقة</td>
                    <td>id, invoice_number, customer_id, items_json, total, Created_by</td>
                </tr>
                <tr>
                    <td><code>settings</code></td>
                    <td>إعدادات النظام</td>
                    <td>id, key, value</td>
                </tr>
                <tr>
                    <td><code>sections</code></td>
                    <td>الأقسام</td>
                    <td>id, section_name, description</td>
                </tr>
            </table>

            <h3>العلاقات بين الجداول</h3>
            <div class="mermaid">
                graph LR
                    A[users] -->|Created_by| B[sales]
                    C[customers] -->|customer_id| B
                    B -->|sale_id| D[sale_items]
                    E[products] -->|product_id| D
                    E -->|category_id| F[categories]
                    E -->|product_id| G[stock_adjustments]
                    C -->|customer_id| H[suspended_sales]
            </div>

            <div class="info">
                <strong>💡 ملاحظة:</strong> حقل <code>Status</code> يُستخدم في معظم الجداول للتحكم في حالة السجل (مفعل/غير مفعل)
            </div>
        </div>

        <!-- Workflow -->
        <div class="section" id="workflow">
            <h2>3. سير العمل (Workflow)</h2>

            <h3>3.1 عملية البيع</h3>
            <div class="mermaid">
                graph TD
                    A[العميل يختار المنتجات] --> B[إضافة للمنتجات للسلة]
                    B --> C{هل يحتاج العميل؟}
                    C -->|نعم| D[اختيار زائر أو عميل مسجل]
                    C -->|لا| E[المتابعة بدون عميل]
                    D --> F[اختيار طريقة الدفع]
                    E --> F
                    F --> G{طريقة الدفع}
                    G -->|نقدي| H[إدخال المبلغ + حساب الباقي]
                    G -->|بطاقة| I[تأكيد الدفع البطاقة]
                    G -->|تقسيم| J[إدخال المبلغ نقدي + بطاقة]
                    H --> K[تأكيد البيع]
                    I --> K
                    J --> K
                    K --> L[تحديث المخزون]
                    L --> M[إنشاء فاتورة]
                    M --> N[عرض رسالة نجاح]
            </div>

            <h3>3.2 سير عمل المخزون</h3>
            <div class="mermaid">
                graph LR
                    A[شراء من المورد] --> B[إضافة المخزون]
                    B --> C[المستودع]
                    C --> D[بيع عبر POS]
                    D --> E[خصم من المخزون]
                    E --> F{المخزون منخفض؟}
                    F -->|نعم| G[تنبيه المخزون المنخفض]
                    F -->|لا| H[متابعة]
                    G --> I[طلب من المورد]
                    I --> A
            </div>

            <h3>3.3 إدارة المصروفات</h3>
            <div class="mermaid">
                graph TD
                    A[إنشاء تصنيف مصروفات] --> B[إضافة مصروف جديد]
                    B --> C[تحديد المبلغ والتاريخ]
                    C --> D[ربط بتصنيف]
                    D --> E[حفظ المصروف]
                    E --> F[تحديث التقارير]
            </div>

            <div class="warning">
                <strong>⚠️Important:</strong> عند حذف عملية بيع، يتم إعادة المخزون للمنتجات تلقائياً
            </div>
        </div>

        <!-- Controllers -->
        <div class="section" id="controllers">
            <h2>4. هيكل التحكمات (Controllers)</h2>

            <table>
                <tr>
                    <th>الكونترولر</th>
                    <th>الوصف</th>
                    <th>الوظائف الرئيسية</th>
                </tr>
                <tr>
                    <td><code>SaleController</code></td>
                    <td>إدارة المبيعات</td>
                    <td>index, pos, store, destroy, suspend, resumeSuspended</td>
                </tr>
                <tr>
                    <td><code>ProductsController</code></td>
                    <td>إدارة المنتجات</td>
                    <td>index, store, update, destroy, show</td>
                </tr>
                <tr>
                    <td><code>CategoryController</code></td>
                    <td>إدارة التصنيفات</td>
                    <td>index, store, update, destroy</td>
                </tr>
                <tr>
                    <td><code>CustomerController</code></td>
                    <td>إدارة العملاء</td>
                    <td>index, store, update, destroy</td>
                </tr>
                <tr>
                    <td><code>SupplierController</code></td>
                    <td>إدارة الموردين</td>
                    <td>index, store, update, destroy</td>
                </tr>
                <tr>
                    <td><code>ExpenseController</code></td>
                    <td>إدارة المصروفات</td>
                    <td>index, store, update, destroy</td>
                </tr>
                <tr>
                    <td><code>StockAdjustmentController</code></td>
                    <td>تعديلات المخزون</td>
                    <td>index, store, show</td>
                </tr>
                <tr>
                    <td><code>UserController</code></td>
                    <td>إدارة المستخدمين</td>
                    <td>index, store, update, destroy</td>
                </tr>
                <tr>
                    <td><code>RoleController</code></td>
                    <td>إدارة الصلاحيات</td>
                    <td>index, store, update, destroy</td>
                </tr>
                <tr>
                    <td><code>SuspendedSaleController</code></td>
                    <td>المبيعات المعلقة</td>
                    <td>index, resume, destroy</td>
                </tr>
            </table>
        </div>

        <!-- Routes -->
        <div class="section" id="routes">
            <h2>5. المسارات (Routes)</h2>

            <h3>المسارات الرئيسية</h3>
            <pre><code>// Authentication
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login');

// Home
Route::get('/home', 'HomeController@index')->name('home');

// Products & Inventory
Route::resource('products', ProductsController::class);
Route::resource('categories', CategoryController::class);
Route::resource('stock_adjustments', StockAdjustmentController::class);

// People
Route::resource('sections', SectionsController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('customers', CustomerController::class);

// Expenses
Route::resource('expense_categories', ExpenseCategoryController::class);
Route::resource('expenses', ExpenseController::class);

// Sales & POS
Route::resource('sales', SaleController::class);
Route::get('pos', 'SaleController@pos')->name('pos');
Route::post('sales/suspend', 'SaleController@suspend')->name('sales.suspend');
Route::get('suspended-sales', 'SuspendedSaleController@index')->name('suspended.index');

// AJAX Routes for POS
Route::get('pos/products', 'SaleController@getProducts')->name('pos.products');
Route::get('pos/products/search', 'SaleController@searchProducts')->name('pos.products.search');
Route::get('pos/products/barcode/{barcode}', 'SaleController@getProductByBarcode')->name('pos.products.barcode');

// Settings & Users
Route::resource('settings', SettingController::class);
Route::resource('roles', RoleController::class);
Route::resource('users', UserController::class);</code></pre>
        </div>

        <!-- Views -->
        <div class="section" id="views">
            <h2>6. الواجهات (Views)</h2>

            <h3>هيكل الواجهات</h3>
            <pre><code>resources/views/
├── layouts/
│   ├── master.blade.php          # القالب الرئيسي
│   ├── header.blade.php          # الهيدر
│   ├── footer.blade.php          # الفوتر
│   └── footer-scripts.blade.php  # سكربتات JavaScript
├── sales/
│   ├── pos.blade.php             # شاشة POS
│   ├── sales.blade.php           # قائمة المبيعات
│   └── index.blade.php           # صفحة المبيعات
├── products/
│   ├── index.blade.php           # قائمة المنتجات
│   └── create.blade.php          # إضافة منتج
├── customers/
│   ├── index.blade.php           # قائمة العملاء
│   └── create.blade.php          # إضافة عميل
├── expenses/
│   ├── index.blade.php           # المصروفات
│   └── create.blade.php          # إضافة مصروف
└── 其他/
    └── ...</code></pre>

            <div class="info">
                <strong>💡Tip:</strong> الواجهات تستخدم Bootstrap 5 وقالب Valex للمظهر
            </div>
        </div>

        <!-- POS Screen -->
        <div class="section" id="pos">
            <h2>7. شاشة POS - التفاصيل التقنية</h2>

            <h3>7.1 هيكل صفحة POS</h3>
            <div class="mermaid">
                graph TB
                    A[صفحة POS] --> B[Header - البحث والعميل]
                    A --> C[Main Content]
                    C --> D[Left Panel - المنتجات]
                    C --> E[Right Panel - السلة]
                    D --> D1[تصنيفات المنتجات]
                    D --> D2[شبكة المنتجات]
                    E --> E1[عناصر السلة]
                    E --> E2[المجموع والضريبة]
                    E --> E3[أزرار الدفع]
            </div>

            <h3>7.2 AJAX Routes للموقع</h3>
            <table>
                <tr>
                    <th>Route</th>
                    <th>الوصف</th>
                    <th>المعلمات</th>
                </tr>
                <tr>
                    <td><code>GET /pos/products</code></td>
                    <td>جلب المنتجات</td>
                    <td>category_id (اختياري)</td>
                </tr>
                <tr>
                    <td><code>GET /pos/products/search</code></td>
                    <td>البحث عن منتج</td>
                    <td>q (استعلام البحث)</td>
                </tr>
                <tr>
                    <td><code>GET /pos/products/barcode/{barcode}</code></td>
                    <td>جلب منتج بالباركود</td>
                    <td>barcode (رقم المنتج)</td>
                </tr>
            </table>

            <h3>7.3 Functions JavaScript الرئيسية</h3>
            <pre><code>// تحميل المنتجات
loadProducts(categoryId)

// إضافة منتج للسلة
addToCartDirect(productId, name, price, stock)

// تحديث السلة
renderCart()
updateTotals()

// معالجة الدفع
openPaymentModal()
processPayment()

// إدارة المبيعات المعلقة
suspendSale()
clearCart()</code></pre>

            <h3>7.4 Cash Handling في POS</h3>
            <ul>
                <li>الضريبة: 15% (يمكن تعديلها في TAX_RATE)</li>
                <li>الخصم: مدعوم (يدوي)</li>
                <li>طرق الدفع: نقدي - بطاقة - تقسيم</li>
                <li>حساب الباقي تلقائي</li>
            </ul>

            <div class="success">
                <strong>✅Keyboard Shortcuts:</strong><br>
                F2 - فتح نافذة الدفع<br>
                F4 - تعليق البيع<br>
                Esc - مسح السلة
            </div>
        </div>

        <!-- How to Modify -->
        <div class="section" id="modify">
            <h2>8. كيفية التعديل والتطوير</h2>

            <h3>8.1 إضافة خاصية جديدة</h3>
            <ol>
                <li><strong>قاعدة البيانات:</strong> إنشاء Migration جديد</li>
                <li><strong>Model:</strong> إنشاء Model جديد إذا لزم الأمر</li>
                <li><strong>Controller:</strong> إضافة الدوال المطلوبة</li>
                <li><strong>Routes:</strong> إضافة المسار في web.php</li>
                <li><strong>View:</strong> إنشاء الواجهة الجديدة</li>
            </ol>

            <h3>8.2 تعديل الضريبة</h3>
            <pre><code>// في ملف resources/views/sales/pos.blade.php
const TAX_RATE = 0.15; // 15% - غير هذا القيمة</code></pre>

            <h3>8.3 إضافة حقل جديد للمنتج</h3>
            <ol>
                <li>إنشاء Migration: <code>php artisan make:migration add_field_to_products_table</code></li>
                <li>تحديث Model في <code>app/Models/Product.php</code></li>
                <li>تحديث Controller والـ View</li>
            </ol>

            <h3>8.4 إضافة طريقة دفع جديدة</h3>
            <ol>
                <li>تحديث Enum في جدول Sales</li>
                <li>تحديث view في payment-modal</li>
                <li>تابع function selectPaymentMethod في JavaScript</li>
            </ol>

            <h3>8.5 تغيير تصميم الواجهة</h3>
            <ul>
                <li>CSS مخصص: أضف في قسم @section('css')</li>
                <li>تعديل القالب: <code>resources/views/layouts/master.blade.php</code></li>
            </ul>

            <h3>8.6 الأوامر المفيدة</h3>
            <pre><code># إنشاء Controller جديد
php artisan make:controller ProductController

# إنشاء Migration جديد
php artisan make:migration create_products_table

# إنشاء Model جديد
php artisan make:model Product

# تشغيل Migrations
php artisan migrate

# عرض الـ Routes
php artisan route:list

# مسح الـ Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear</code></pre>

            <div class="warning">
                <strong>⚠️Important Notes:</strong>
                <ul>
                    <li>Always backup the database before making changes</li>
                    <li>Use proper naming conventions for files and variables</li>
                    <li>Follow Laravel best practices</li>
                    <li>Test all changes thoroughly before deployment</li>
                </ul>
            </div>
        </div>

        <!-- Appendix -->
        <div class="section">
            <h2>📎 ملحق: مخطط قاعدة البيانات التفصيلي</h2>
            
            <h3>جدول Products</h3>
            <pre><code>id              - BIGINT (PK, AutoIncrement)
Product_name    - VARCHAR(999)
category_id     - BIGINT (FK)
sku             - VARCHAR(50) UNIQUE
barcode         - VARCHAR(50) UNIQUE
photo           - VARCHAR(999)
description     - TEXT
cost_price      - DECIMAL(10,2)
sell_price      - DECIMAL(10,2)
tax_rate        - DECIMAL(5,2)
reorder_point   - INTEGER
wac             - DECIMAL(10,2)
stock_qty       - INTEGER
Status          - VARCHAR(50) - 'مفعل' or 'غير مفعل'
Created_by      - VARCHAR(999)
created_at      - TIMESTAMP
updated_at      - TIMESTAMP</code></pre>

            <h3>جدول Sales</h3>
            <pre><code>id              - BIGINT (PK, AutoIncrement)
invoice_number  - VARCHAR(50) UNIQUE
customer_id     - BIGINT (FK, nullable)
subtotal        - DECIMAL(10,2)
tax_amount      - DECIMAL(10,2)
discount        - DECIMAL(10,2)
total           - DECIMAL(10,2)
payment_method  - ENUM('cash', 'card', 'split')
cash_amount     - DECIMAL(10,2)
card_amount     - DECIMAL(10,2)
paid_amount     - DECIMAL(10,2)
change_due      - DECIMAL(10,2)
Status          - VARCHAR(50)
Created_by      - VARCHAR(999)
created_at      - TIMESTAMP
updated_at      - TIMESTAMP</code></pre>
        </div>

        <footer>
            <p>📝 توثيق نظام نقاط البيع - POS System Documentation</p>
            <p>تم الإنشاء في مارس 2026</p>
        </footer>
    </div>

    <script>
        mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            securityLevel: 'loose',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true
            }
        });
    </script>
</body>
</html>