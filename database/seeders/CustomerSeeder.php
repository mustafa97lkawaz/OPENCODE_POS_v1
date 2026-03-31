<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = [
            [
                'Customer_name' => 'احمد محمد',
                'phone' => '0501234567',
                'email' => 'ahmed@example.com',
                'address' => 'الرياض - حي الازدهار',
                'type' => 'account',
                'account_balance' => 0.00,
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Customer_name' => 'خالد Saleh',
                'phone' => '0502345678',
                'email' => 'khaled@example.com',
                'address' => 'جدة - حي الصفا',
                'type' => 'account',
                'account_balance' => 50.00,
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Customer_name' => 'عمر Hassan',
                'phone' => '0503456789',
                'email' => 'omar@example.com',
                'address' => 'الدمام - حي ال又一',
                'type' => 'walk-in',
                'account_balance' => 0.00,
                'Status' => 'مفعل',
                'Created_by' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customers')->insert($customers);
        
        echo "Customers seeded successfully!\n";
    }
}
