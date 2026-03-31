<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for POS system
        $permissions = [
            // Dashboard
            'لوحة التحكم',
            
            // POS
            'شاشة POS',
            'المبيعات',
            'المبيعات المعلقة',
            
            // Inventory
            'المنتجات',
            'التصنيفات',
            'تعديلات المخزون',
            
            // People
            'العملاء',
            'الموردين',
            
            // Expenses
            'تصنيفات المصروفات',
            'المصروفات',
            
            // Settings
            'الاعدادات',
            'قائمة المستخدمين',
            'صلاحيات المستخدمين',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Admin Role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Create Manager Role
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo([
            'لوحة التحكم',
            'شاشة POS',
            'المبيعات',
            'المبيعات المعلقة',
            'المنتجات',
            'التصنيفات',
            'تعديلات المخزون',
            'العملاء',
            'الموردين',
            'تصنيفات المصروفات',
            'المصروفات',
        ]);

        // Create Cashier Role
        $cashierRole = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $cashierRole->givePermissionTo([
            'لوحة التحكم',
            'شاشة POS',
            'المبيعات',
        ]);

        // Create User Role
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->givePermissionTo([
            'لوحة التحكم',
        ]);

        echo "Permissions and Roles seeded successfully!\n";
    }
}
