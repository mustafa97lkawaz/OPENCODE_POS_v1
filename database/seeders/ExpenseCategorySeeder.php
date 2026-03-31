<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'Category_name' => 'ايجار',
                'Description' => 'ايجار المحل والمكان',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'رواتب',
                'Description' => 'رواتب الموظفين والعمال',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'كهرباء',
                'Description' => 'فواتير الكهرباء',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'مياه',
                'Description' => 'فواتير المياه',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'انترنت',
                'Description' => 'خدمات الاتصال والانترنت',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'صيانة',
                'Description' => 'صيانة المعدات والاجهزة',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'مستلزمات',
                'Description' => 'مستلزمات العمل اليومية',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'اخرى',
                'Description' => 'مصاريف متنوعة',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('expense_categories')->insert($categories);
        
        echo "Expense Categories seeded successfully!\n";
    }
}
