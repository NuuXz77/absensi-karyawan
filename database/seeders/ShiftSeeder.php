<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'nama_shift' => 'Shift Pagi',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '16:00:00',
                'toleransi_menit' => 15,
                'status' => 'active',
            ],
            [
                'nama_shift' => 'Shift Siang',
                'jam_masuk' => '13:00:00',
                'jam_pulang' => '21:00:00',
                'toleransi_menit' => 15,
                'status' => 'active',
            ],
            [
                'nama_shift' => 'Shift Malam',
                'jam_masuk' => '21:00:00',
                'jam_pulang' => '05:00:00',
                'toleransi_menit' => 15,
                'status' => 'active',
            ],
            [
                'nama_shift' => 'Shift Full Time',
                'jam_masuk' => '09:00:00',
                'jam_pulang' => '18:00:00',
                'toleransi_menit' => 10,
                'status' => 'active',
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }

        $this->command->info('4 shift berhasil dibuat');
    }
}
