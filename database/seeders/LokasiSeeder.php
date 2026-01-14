<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lokasis = [
            [
                'nama_lokasi' => 'Kantor Pusat Jakarta',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius_meter' => 100,
                'status' => 'active',
            ],
            [
                'nama_lokasi' => 'Cabang Surabaya',
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'radius_meter' => 150,
                'status' => 'active',
            ],
            [
                'nama_lokasi' => 'Cabang Bandung',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'radius_meter' => 100,
                'status' => 'active',
            ],
            [
                'nama_lokasi' => 'Warehouse Tangerang',
                'latitude' => -6.178306,
                'longitude' => 106.631889,
                'radius_meter' => 200,
                'status' => 'active',
            ],
        ];

        foreach ($lokasis as $lokasi) {
            Lokasi::create($lokasi);
        }

        $this->command->info('4 lokasi berhasil dibuat');
    }
}
