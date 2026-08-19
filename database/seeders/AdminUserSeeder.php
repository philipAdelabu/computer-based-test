<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@votingsystem.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Officer User',
            'email' => 'officer@votingsystem.com',
            'password' => Hash::make('password123'),
            'role' => 'officer'
        ]);

        User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@votingsystem.com',
            'password' => Hash::make('password123'),
            'role' => 'viewer'
        ]);
    }
}