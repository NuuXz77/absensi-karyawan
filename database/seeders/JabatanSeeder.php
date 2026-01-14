<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itDept = Departemen::where('kode_departemen', 'IT')->first();
        $hrDept = Departemen::where('kode_departemen', 'HR')->first();
        $finDept = Departemen::where('kode_departemen', 'FIN')->first();
        $mktDept = Departemen::where('kode_departemen', 'MKT')->first();
        $opsDept = Departemen::where('kode_departemen', 'OPS')->first();

        $jabatans = [
            // IT Department
            ['kode_jabatan' => 'IT-001', 'nama_jabatan' => 'Senior Developer', 'departemen_id' => $itDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'IT-002', 'nama_jabatan' => 'Junior Developer', 'departemen_id' => $itDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'IT-003', 'nama_jabatan' => 'System Analyst', 'departemen_id' => $itDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'IT-004', 'nama_jabatan' => 'UI/UX Designer', 'departemen_id' => $itDept->id, 'status' => 'active'],
            
            // HR Department
            ['kode_jabatan' => 'HR-001', 'nama_jabatan' => 'HR Manager', 'departemen_id' => $hrDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'HR-002', 'nama_jabatan' => 'HR Staff', 'departemen_id' => $hrDept->id, 'status' => 'active'],
            
            // Finance Department
            ['kode_jabatan' => 'FIN-001', 'nama_jabatan' => 'Finance Manager', 'departemen_id' => $finDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'FIN-002', 'nama_jabatan' => 'Accounting Staff', 'departemen_id' => $finDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'FIN-003', 'nama_jabatan' => 'Payroll Staff', 'departemen_id' => $finDept->id, 'status' => 'active'],
            
            // Marketing Department
            ['kode_jabatan' => 'MKT-001', 'nama_jabatan' => 'Marketing Manager', 'departemen_id' => $mktDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'MKT-002', 'nama_jabatan' => 'Marketing Executive', 'departemen_id' => $mktDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'MKT-003', 'nama_jabatan' => 'Sales Executive', 'departemen_id' => $mktDept->id, 'status' => 'active'],
            
            // Operations Department
            ['kode_jabatan' => 'OPS-001', 'nama_jabatan' => 'Operations Supervisor', 'departemen_id' => $opsDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'OPS-002', 'nama_jabatan' => 'Operations Staff', 'departemen_id' => $opsDept->id, 'status' => 'active'],
            ['kode_jabatan' => 'OPS-003', 'nama_jabatan' => 'Warehouse Manager', 'departemen_id' => $opsDept->id, 'status' => 'active'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::create($jabatan);
        }

        $this->command->info('15 jabatan berhasil dibuat');
    }
}
