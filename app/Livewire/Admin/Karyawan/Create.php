<?php

namespace App\Livewire\Admin\Karyawan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\WajahKaryawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('components.layouts.app')]
class Create extends Component
{
    use WithFileUploads;

    // Data Pribadi
    public $nip;
    public $id_card = '';
    public $nama_lengkap;
    public $email;
    public $tanggal_lahir;
    public $jenis_kelamin;
    public $no_telepon;
    public $alamat;
    public $foto_karyawan;

    // Data Akun & Pekerjaan
    public $username = '';
    public $generatedPassword = '';
    public $jabatan = '';
    public $departemen = '';
    public $status = 'active';

    // Face Recognition
    public $faceEmbedding;

    protected $rules = [
        'nip' => 'required|unique:karyawan,nip',
        'id_card' => 'required|unique:karyawan,id_card',
        'nama_lengkap' => 'required|min:3',
        'email' => 'required|email|unique:karyawan,email',
        'tanggal_lahir' => 'required|date',
        'jenis_kelamin' => 'required|in:L,P',
        'no_telepon' => 'nullable|numeric',
        'alamat' => 'nullable|string',
        'foto_karyawan' => 'nullable|image|max:2048',
        'username' => 'required|unique:users,username',
        'jabatan' => 'required',
        'departemen' => 'required',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'nip.required' => 'NIP wajib diisi',
        'nip.unique' => 'NIP sudah terdaftar',
        'id_card.required' => 'ID Card wajib diisi',
        'id_card.unique' => 'ID Card sudah terdaftar',
        'nama_lengkap.required' => 'Nama lengkap wajib diisi',
        'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah terdaftar',
        'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
        'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
        'foto_karyawan.image' => 'File harus berupa gambar',
        'foto_karyawan.max' => 'Ukuran foto maksimal 2MB',
        'username.required' => 'Username wajib diisi',
        'username.unique' => 'Username sudah terdaftar',
        'jabatan.required' => 'Jabatan wajib diisi',
        'departemen.required' => 'Departemen wajib dipilih',
        'status.required' => 'Status wajib dipilih',
    ];

    public function mount()
    {
        $this->generatePassword();
    }

    public function updatedJabatan()
    {
        $this->generateIdCard();
    }

    public function updatedDepartemen()
    {
        $this->generateIdCard();
    }

    public function generateIdCard()
    {
        if (empty($this->jabatan) || empty($this->departemen)) {
            $this->id_card = '';
            $this->username = '';
            return;
        }

        // Mapping inisial jabatan
        $jabatanInisial = [
            'Staff' => 'ST',
            'Supervisor' => 'SV',
            'Manager' => 'MG',
            'Director' => 'DR',
            'Junior' => 'JR',
            'Senior' => 'SR',
        ];

        // Ambil inisial jabatan (default 2 huruf pertama jika tidak ada di mapping)
        $inisial = $jabatanInisial[$this->jabatan] ?? strtoupper(substr($this->jabatan, 0, 2));
        
        // Tahun sekarang
        $year = date('Y');

        // Hitung jumlah karyawan dengan jabatan dan departemen yang sama
        $count = Karyawan::where('jabatan', $this->jabatan)
            ->where('departemen', $this->departemen)
            ->count();

        // Nomor urut (count + 1) dengan format 3 digit
        $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        // Format: INISIAL-TAHUNNOMOR (contoh: ST-2025001)
        $this->id_card = "{$inisial}-{$year}{$number}";
        $this->username = $this->id_card;
    }

    public function generatePassword()
    {
        $this->generatedPassword = strtoupper(Str::random(6));
    }

    public function refreshPassword()
    {
        $this->generatePassword();
        session()->flash('info', 'Password berhasil di-refresh!');
    }

    public function copyPassword()
    {
        $this->dispatch('password-copied', password: $this->generatedPassword);
    }

    public function removeFoto()
    {
        $this->foto_karyawan = null;
        $this->faceEmbedding = null;
    }

    #[On('face-detected')]
    public function handleFaceDetection($embedding)
    {
        $this->faceEmbedding = json_encode($embedding);
    }

    public function save()
    {
        $this->validate();

        try {
            // 1. Create User
            $user = User::create([
                'username' => $this->username,
                'password' => Hash::make($this->generatedPassword),
                'role' => 'karyawan',
                'status' => 'active',
                'harus_mengganti_password' => true,
            ]);

            // 2. Upload foto if exists
            $fotoPath = null;
            if ($this->foto_karyawan) {
                $fotoPath = $this->foto_karyawan->store('karyawan/foto', 'public');
            }

            // 3. Create Karyawan
            $karyawan = Karyawan::create([
                'user_id' => $user->id,
                'nip' => $this->nip,
                'id_card' => $this->id_card,
                'nama_lengkap' => $this->nama_lengkap,
                'email' => $this->email,
                'tanggal_lahir' => $this->tanggal_lahir,
                'jenis_kelamin' => $this->jenis_kelamin,
                'no_telepon' => $this->no_telepon,
                'alamat' => $this->alamat,
                'foto_karyawan' => $fotoPath,
                'jabatan' => $this->jabatan,
                'departemen' => $this->departemen,
                'status' => $this->status,
            ]);

            // 4. Save Face Embedding if exists
            if ($this->faceEmbedding && $fotoPath) {
                WajahKaryawan::create([
                    'karyawan_id' => $karyawan->id,
                    'foto_path' => $fotoPath,
                    'face_embedding' => $this->faceEmbedding,
                ]);
            }

            session()->flash('success', "Karyawan berhasil ditambahkan! Username: {$this->username}, Password: {$this->generatedPassword}");
            return redirect()->route('admin.karyawan.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    #[Title('Tambah Karyawan')]
    public function render()
    {
        return view('livewire.admin.karyawan.create');
    }
}
