<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@lafabrique.fr'],
            [
                'name' => 'Admin',
                'role' => UserRole::Admin,
                'account_status' => AccountStatus::Active,
                'password' => Hash::make(env('ADMIN_SEED_PASSWORD', 'changeme-local')),
                'email_verified_at' => now(),
            ]
        );
    }
}
