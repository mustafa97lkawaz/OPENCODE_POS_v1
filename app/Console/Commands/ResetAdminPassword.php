<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password';
    protected $description = 'Reset admin user password';

    public function handle()
    {
        $user = User::where('email', 'admin@admin.com')->first();
        
        if ($user) {
            $user->password = Hash::make('password123');
            $user->save();
            $this->info('Admin password reset to: password123');
            return 0;
        }
        
        $this->error('Admin user not found');
        return 1;
    }
}
