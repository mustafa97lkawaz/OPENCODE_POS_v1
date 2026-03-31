<?php

namespace Database\Seeders; // <-- 1. Added the missing namespace

use Illuminate\Database\Seeder;
use App\Models\User; // <-- 2. Changed App\User to App\Models\User (Laravel 8+ standard)
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $user = User::create([
            'name' => 'samirgamal', 
            'email' => 'samir.gamal77@yahoo.com',
            'password' => bcrypt('123456'),
            'roles_name' => ["owner"], // Ensure 'roles_name' is in your User model's $fillable array!
            'Status' => 'مفعل',       // Ensure 'Status' is in your User model's $fillable array!
        ]);
  
        $role = Role::create(['name' => 'owner']);
   
        $permissions = Permission::pluck('id','id')->all();
  
        $role->syncPermissions($permissions);
   
        $user->assignRole([$role->id]);
    }
}