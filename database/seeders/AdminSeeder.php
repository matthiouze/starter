<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'firstname' => 'Admin',
                'lastname' => 'Admin',
                'password' => Hash::make('azeaze'),
                'email_verified_at' => now(),
            ],
        );

        $superAdminRole = config('filament-shield.super_admin.name', 'super_admin');

        Role::findOrCreate($superAdminRole, 'web');

        $user->assignRole($superAdminRole);
    }
}
