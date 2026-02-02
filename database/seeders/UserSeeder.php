<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Create Admin
        User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin',
            'email' => 'admin@rental.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
        ]);

        $users = [
            [
                'role_id' => $userRole->id,
                'name' => 'Alpine',
                'email' => 'alpine@mail.com',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}