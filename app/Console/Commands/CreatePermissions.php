<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreatePermissions extends Command
{
    protected $signature = 'permissions:create';
    protected $description = 'Create permissions for roles';

    public function handle()
    {
        $permissions = [
            'عرض صلاحية',
            'اضافة صلاحية',
            'تعديل صلاحية',
            'حذف صلاحية',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
            $this->info("Created permission: $perm");
        }

        $this->info('All permissions created successfully!');
        return 0;
    }
}
