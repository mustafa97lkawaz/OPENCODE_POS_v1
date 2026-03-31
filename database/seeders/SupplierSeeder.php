<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            [
                'Supplier_name' => 'شركة المشروبات العالمية',
                'phone' => '0111234567',
                'email' => 'info@drinks.com',
                'address' => 'الرياض - المنطقة الصناعية',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Supplier_name' => 'موردي FOOD',
                'phone' => '0112345678',
                'email' => 'orders@foodsupply.com',
                'address' => 'جدة - ميناء جدة',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Supplier_name' => 'شركة الحللويات والمخبوزات',
                'phone' => '0113456789',
                'email' => 'sales@sweets.com',
                'address' => 'الدمام - حي الحtoa',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
        
        echo "Suppliers seeded successfully!\n";
    }
}
