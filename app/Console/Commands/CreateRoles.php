<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRoles extends Command
{
    protected $signature = 'roles:create';
    protected $description = 'Create default roles with permissions';

    public function handle()
    {
        // Admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $allPermissions = Permission::pluck('id', 'id')->all();
        $adminRole->givePermissionTo($allPermissions);
        $this->info("Admin role created with all permissions");

        // Manager role
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerPermissions = [
            'اضافة صلاحية',
            'عرض صلاحية',
        ];
        foreach ($managerPermissions as $permName) {
            $perm = Permission::where('name', $permName)->first();
            if ($perm) {
                $managerRole->givePermissionTo($perm);
            }
        }
        $this->info("Manager role created");

        // Cashier role (limited permissions)
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $this->info("Cashier role created");

        $this->info('Default roles created successfully!');
        return 0;
    }
}
