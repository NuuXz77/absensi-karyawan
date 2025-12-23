<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users with role karyawan
        $karyawan1 = User::where('username', 'karyawan1')->first();
        $karyawan2 = User::where('username', 'karyawan2')->first();
        $karyawan3 = User::where('username', 'karyawan3')->first();

        $karyawanData = [
            [
                'user_id' => $karyawan1->id,
                'nip' => 'NIP2024001',
                'id_card' => '3201234567890001',
                'nama_lengkap' => 'Ahmad Fadli',
                'email' => 'ahmad.fadli@company.com',
                'tanggal_lahir' => '1990-05-15',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567001',
                'jabatan' => 'Senior Developer',
                'departemen' => 'IT',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'status' => 'active',
            ],
            [
                'user_id' => $karyawan2->id,
                'nip' => 'NIP2024002',
                'id_card' => '3201234567890002',
                'nama_lengkap' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@company.com',
                'tanggal_lahir' => '1992-08-20',
                'jenis_kelamin' => 'P',
                'no_telepon' => '081234567002',
                'jabatan' => 'HR Manager',
                'departemen' => 'HR',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta',
                'status' => 'active',
            ],
            [
                'user_id' => $karyawan3->id,
                'nip' => 'NIP2024003',
                'id_card' => '3201234567890003',
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi.santoso@company.com',
                'tanggal_lahir' => '1988-03-10',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567003',
                'jabatan' => 'Finance Manager',
                'departemen' => 'Finance',
                'alamat' => 'Jl. Gatot Subroto No. 78, Jakarta',
                'status' => 'active',
            ],
        ];

        // Create additional users and karyawan
        $additionalKaryawan = [
            [
                'username' => 'dewi.lestari',
                'nip' => 'NIP2024004',
                'id_card' => '3201234567890004',
                'nama_lengkap' => 'Dewi Lestari',
                'email' => 'dewi.lestari@company.com',
                'tanggal_lahir' => '1995-11-25',
                'jenis_kelamin' => 'P',
                'no_telepon' => '081234567004',
                'jabatan' => 'Marketing Executive',
                'departemen' => 'Marketing',
                'alamat' => 'Jl. Thamrin No. 12, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'eko.prasetyo',
                'nip' => 'NIP2024005',
                'id_card' => '3201234567890005',
                'nama_lengkap' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@company.com',
                'tanggal_lahir' => '1991-07-18',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567005',
                'jabatan' => 'Operations Supervisor',
                'departemen' => 'Operations',
                'alamat' => 'Jl. Rasuna Said No. 89, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'fitri.handayani',
                'nip' => 'NIP2024006',
                'id_card' => '3201234567890006',
                'nama_lengkap' => 'Fitri Handayani',
                'email' => 'fitri.handayani@company.com',
                'tanggal_lahir' => '1993-02-14',
                'jenis_kelamin' => 'P',
                'no_telepon' => '081234567006',
                'jabatan' => 'Junior Developer',
                'departemen' => 'IT',
                'alamat' => 'Jl. Kuningan No. 34, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'gunawan.putra',
                'nip' => 'NIP2024007',
                'id_card' => '3201234567890007',
                'nama_lengkap' => 'Gunawan Putra',
                'email' => 'gunawan.putra@company.com',
                'tanggal_lahir' => '1989-09-30',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567007',
                'jabatan' => 'HR Staff',
                'departemen' => 'HR',
                'alamat' => 'Jl. Senayan No. 56, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'hesti.rahayu',
                'nip' => 'NIP2024008',
                'id_card' => '3201234567890008',
                'nama_lengkap' => 'Hesti Rahayu',
                'email' => 'hesti.rahayu@company.com',
                'tanggal_lahir' => '1994-12-05',
                'jenis_kelamin' => 'P',
                'no_telepon' => '081234567008',
                'jabatan' => 'Accounting Staff',
                'departemen' => 'Finance',
                'alamat' => 'Jl. Menteng No. 67, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'indra.wijaya',
                'nip' => 'NIP2024009',
                'id_card' => '3201234567890009',
                'nama_lengkap' => 'Indra Wijaya',
                'email' => 'indra.wijaya@company.com',
                'tanggal_lahir' => '1987-04-22',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567009',
                'jabatan' => 'Marketing Manager',
                'departemen' => 'Marketing',
                'alamat' => 'Jl. Kemang No. 23, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'julia.putri',
                'nip' => 'NIP2024010',
                'id_card' => '3201234567890010',
                'nama_lengkap' => 'Julia Putri',
                'email' => 'julia.putri@company.com',
                'tanggal_lahir' => '1996-06-08',
                'jenis_kelamin' => 'P',
                'no_telepon' => '081234567010',
                'jabatan' => 'Operations Staff',
                'departemen' => 'Operations',
                'alamat' => 'Jl. Cikini No. 90, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'kevin.atmaja',
                'nip' => 'NIP2024011',
                'id_card' => '3201234567890011',
                'nama_lengkap' => 'Kevin Atmaja',
                'email' => 'kevin.atmaja@company.com',
                'tanggal_lahir' => '1992-01-17',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567011',
                'jabatan' => 'UI/UX Designer',
                'departemen' => 'IT',
                'alamat' => 'Jl. Blok M No. 45, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'lina.susanti',
                'nip' => 'NIP2024012',
                'id_card' => '3201234567890012',
                'nama_lengkap' => 'Lina Susanti',
                'email' => 'lina.susanti@company.com',
                'tanggal_lahir' => '1994-08-29',
                'jenis_kelamin' => 'P',
                'no_telepon' => '081234567012',
                'jabatan' => 'Sales Executive',
                'departemen' => 'Marketing',
                'alamat' => 'Jl. Pondok Indah No. 78, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'michael.tan',
                'nip' => 'NIP2024013',
                'id_card' => '3201234567890013',
                'nama_lengkap' => 'Michael Tan',
                'email' => 'michael.tan@company.com',
                'tanggal_lahir' => '1990-10-12',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567013',
                'jabatan' => 'System Analyst',
                'departemen' => 'IT',
                'alamat' => 'Jl. Kelapa Gading No. 101, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'nina.safitri',
                'nip' => 'NIP2024014',
                'id_card' => '3201234567890014',
                'nama_lengkap' => 'Nina Safitri',
                'email' => 'nina.safitri@company.com',
                'tanggal_lahir' => '1993-05-21',
                'jenis_kelamin' => 'P',
                'no_telepon' => '081234567014',
                'jabatan' => 'Payroll Staff',
                'departemen' => 'Finance',
                'alamat' => 'Jl. Tebet No. 33, Jakarta',
                'status' => 'active',
            ],
            [
                'username' => 'oscar.pranata',
                'nip' => 'NIP2024015',
                'id_card' => '3201234567890015',
                'nama_lengkap' => 'Oscar Pranata',
                'email' => 'oscar.pranata@company.com',
                'tanggal_lahir' => '1991-03-16',
                'jenis_kelamin' => 'L',
                'no_telepon' => '081234567015',
                'jabatan' => 'Warehouse Manager',
                'departemen' => 'Operations',
                'alamat' => 'Jl. Pluit No. 56, Jakarta',
                'status' => 'active',
            ],
        ];

        // Create karyawan for existing users
        foreach ($karyawanData as $data) {
            Karyawan::create([
                'user_id' => $data['user_id'],
                'id_card' => $data['id_card'],
                'nip' => $data['nip'],
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'no_telepon' => $data['no_telepon'],
                'foto_karyawan' => null,
                'jabatan' => $data['jabatan'],
                'departemen' => $data['departemen'],
                'alamat' => $data['alamat'],
                'status' => $data['status'],
            ]);
        }

        // Create new users and karyawan
        foreach ($additionalKaryawan as $data) {
            $user = User::create([
                'username' => $data['username'],
                'password' => bcrypt('password123'),
                'role' => 'karyawan',
                'harus_mengganti_password' => true,
                'status' => 'active',
            ]);

            Karyawan::create([
                'user_id' => $user->id,
                'id_card' => $data['id_card'],
                'nip' => $data['nip'],
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'no_telepon' => $data['no_telepon'],
                'foto_karyawan' => null,
                'jabatan' => $data['jabatan'],
                'departemen' => $data['departemen'],
                'alamat' => $data['alamat'],
                'status' => $data['status'],
            ]);
        }

        $this->command->info('15 karyawan berhasil dibuat (3 dari UserSeeder + 12 baru) dengan password default: password123');
    }
}
