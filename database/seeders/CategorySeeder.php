<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CategorySeeder extends Seeder
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
                'Category_name' => 'مشروبات ساخنة',
                'Description' => 'مشروبات ساخنة مثل الشاي والقهوة',
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'مشروبات باردة',
                'Description' => 'مشروبات باردة وعصائر',
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'وجبات رئيسية',
                'Description' => 'وجبات رئيسية كاملة',
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'وجبات خفيفة',
                'Description' => 'وجبات خفيفة وسريعة',
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'حلويات',
                'Description' => 'حلويات و desserts',
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Category_name' => 'م snacks',
                'Description' => 'م snacks和小吃',
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
        
        echo "Categories seeded successfully!\n";
    }
}
