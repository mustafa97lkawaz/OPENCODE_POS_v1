<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Order matters - seed data first, then permissions
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            ExpenseCategorySeeder::class,
            PermissionSeeder::class,
            CreateAdminUserSeeder::class,
        ]);
    }
}
