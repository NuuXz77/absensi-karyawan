<?php

namespace App\Livewire\Admin\Karyawan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\WajahKaryawan;
use App\Models\Departemen;
use App\Models\Jabatan;
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

    public function generateIdCard()
    {
        if (empty($this->jabatan)) {
            $this->id_card = '';
            $this->username = '';
            return;
        }

        // Ambil data jabatan dari database untuk mendapatkan kode
        $jabatanData = Jabatan::find($this->jabatan);
        if (!$jabatanData) {
            return;
        }

        // Gunakan kode jabatan dari database
        $inisial = $jabatanData->kode_jabatan;
        
        // Tahun sekarang
        $year = date('Y');

        // Hitung jumlah karyawan dengan jabatan yang sama
        $count = Karyawan::where('jabatan_id', $this->jabatan)->count();

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

            // 3. Ambil departemen dari jabatan
            $jabatanData = Jabatan::find($this->jabatan);
            $departemen_id = $jabatanData ? $jabatanData->departemen_id : null;

            // 4. Create Karyawan
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
                'jabatan_id' => $this->jabatan,
                'departemen_id' => $departemen_id,
                'status' => $this->status,
            ]);

            // 5. Save Face Embedding if exists
            if ($this->faceEmbedding && $fotoPath) {
                WajahKaryawan::create([
                    'karyawan_id' => $karyawan->id,
                    'foto_path' => $fotoPath,
                    'face_embedding' => $this->faceEmbedding,
                ]);
            }

            session()->flash('success', "Karyawan berhasil ditambahkan! Username: {$this->username}, Password: {$this->generatedPassword}");
            return $this->redirect(route('admin.karyawan.index'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    #[Title('Tambah Karyawan')]
    public function render()
    {
        $jabatans = Jabatan::with('departemen')
            ->where('status', 'active')
            ->orderBy('nama_jabatan')
            ->get();

        return view('livewire.admin.karyawan.create', [
            'jabatans' => $jabatans
        ]);
    }
}
