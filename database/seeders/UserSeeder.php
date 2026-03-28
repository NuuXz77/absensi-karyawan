<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Single source of truth for seeded users and demo credentials.
     */
    private static function seedUsers(): array
    {
        return [
            [
                'username' => 'admin',
                'plain_password' => 'admin123',
                'role' => 'admin',
                'harus_mengganti_password' => false,
                'status' => 'active',
            ],
            [
                'username' => 'karyawan1',
                'plain_password' => 'password123',
                'role' => 'karyawan',
                'harus_mengganti_password' => true,
                'status' => 'active',
            ],
            [
                'username' => 'karyawan2',
                'plain_password' => 'password123',
                'role' => 'karyawan',
                'harus_mengganti_password' => true,
                'status' => 'active',
            ],
            [
                'username' => 'karyawan3',
                'plain_password' => 'password123',
                'role' => 'karyawan',
                'harus_mengganti_password' => true,
                'status' => 'active',
            ],
            [
                'username' => 'karyawan_inactive',
                'plain_password' => 'password123',
                'role' => 'karyawan',
                'harus_mengganti_password' => true,
                'status' => 'inactive',
            ],
        ];
    }

    /**
     * Demo accounts shown on login page (active admin + active karyawan).
     */
    public static function demoLoginAccounts(): array
    {
        $admin = null;
        $karyawan = null;

        foreach (self::seedUsers() as $user) {
            if ($user['status'] !== 'active') {
                continue;
            }

            if ($user['role'] === 'admin' && $admin === null) {
                $admin = [
                    'label' => 'Admin',
                    'username' => $user['username'],
                    'password' => $user['plain_password'],
                ];
            }

            if ($user['role'] === 'karyawan' && $karyawan === null) {
                $karyawan = [
                    'label' => 'Karyawan',
                    'username' => $user['username'],
                    'password' => $user['plain_password'],
                ];
            }
        }

        return array_values(array_filter([$admin, $karyawan]));
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::seedUsers() as $user) {
            User::create([
                'username' => $user['username'],
                'password' => Hash::make($user['plain_password']),
                'role' => $user['role'],
                'harus_mengganti_password' => $user['harus_mengganti_password'],
                'status' => $user['status'],
            ]);
        }
    }
}
