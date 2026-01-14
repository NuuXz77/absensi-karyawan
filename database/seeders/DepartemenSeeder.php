<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemens = [
            ['kode_departemen' => 'IT', 'nama_departemen' => 'IT', 'status' => 'active'],
            ['kode_departemen' => 'HR', 'nama_departemen' => 'HR', 'status' => 'active'],
            ['kode_departemen' => 'FIN', 'nama_departemen' => 'Finance', 'status' => 'active'],
            ['kode_departemen' => 'MKT', 'nama_departemen' => 'Marketing', 'status' => 'active'],
            ['kode_departemen' => 'OPS', 'nama_departemen' => 'Operations', 'status' => 'active'],
        ];

        foreach ($departemens as $departemen) {
            Departemen::create($departemen);
        }

        $this->command->info('5 departemen berhasil dibuat');
    }
}
