<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'harus_mengganti_password' => false,
            'status' => 'active',
        ]);

        // Karyawan Users
        User::create([
            'username' => 'karyawan1',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'harus_mengganti_password' => true,
            'status' => 'active',
        ]);

        User::create([
            'username' => 'karyawan2',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'harus_mengganti_password' => true,
            'status' => 'active',
        ]);

        User::create([
            'username' => 'karyawan3',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'harus_mengganti_password' => true,
            'status' => 'active',
        ]);

        // Inactive User
        User::create([
            'username' => 'karyawan_inactive',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'harus_mengganti_password' => true,
            'status' => 'inactive',
        ]);
    }
}
