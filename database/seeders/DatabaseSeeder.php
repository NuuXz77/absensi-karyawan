<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Master tables first
            DepartemenSeeder::class,
            JabatanSeeder::class,
            ShiftSeeder::class,
            LokasiSeeder::class,
            
            // User and Karyawan tables
            UserSeeder::class,
            KaryawanSeeder::class,
        ]);
    }
}
