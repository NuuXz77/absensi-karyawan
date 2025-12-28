<?php

namespace App\Livewire\Admin\Karyawan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\WajahKaryawan;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    use WithFileUploads;

    public Karyawan $karyawan;

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
    public $existing_foto;

    // Data Akun & Pekerjaan
    public $username = '';
    public $jabatan = '';
    public $status = 'active';
    public $generatedPassword = null;
    public $passwordChanged = false;

    // Face Recognition
    public $faceEmbedding;

    protected function rules()
    {
        return [
            'nip' => 'required|unique:karyawan,nip,' . $this->karyawan->id,
            'id_card' => 'required|unique:karyawan,id_card,' . $this->karyawan->id,
            'nama_lengkap' => 'required|min:3',
            'email' => 'required|email|unique:karyawan,email,' . $this->karyawan->id,
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telepon' => 'nullable|numeric',
            'alamat' => 'nullable|string',
            'foto_karyawan' => 'nullable|image|max:2048',
            'username' => 'required|unique:users,username,' . $this->karyawan->user_id,
            'jabatan' => 'required',
            'status' => 'required|in:active,inactive',
        ];
    }

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

    public function mount($id)
    {
        $this->karyawan = Karyawan::with(['user', 'jabatan', 'departemen', 'wajah'])->findOrFail($id);
        
        // Fill form with existing data
        $this->nip = $this->karyawan->nip;
        $this->id_card = $this->karyawan->id_card;
        $this->nama_lengkap = $this->karyawan->nama_lengkap;
        $this->email = $this->karyawan->email;
        $this->tanggal_lahir = $this->karyawan->tanggal_lahir;
        $this->jenis_kelamin = $this->karyawan->jenis_kelamin;
        $this->no_telepon = $this->karyawan->no_telepon;
        $this->alamat = $this->karyawan->alamat;
        $this->existing_foto = $this->karyawan->foto_karyawan;
        
        $this->username = $this->karyawan->user->username ?? '';
        $this->jabatan = $this->karyawan->jabatan_id;
        $this->status = $this->karyawan->status;
        
        // Set placeholder for password (not showing actual password)
        $this->generatedPassword = '******';
        $this->passwordChanged = false;
    }

    public function refreshPassword()
    {
        $this->generatedPassword = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->passwordChanged = true;
        $this->dispatch('password-generated');
    }

    public function copyPassword()
    {
        $this->dispatch('copy-to-clipboard', text: $this->generatedPassword);
    }

    public function updatedJabatan()
    {
        $this->generateIdCard();
    }

    public function generateIdCard()
    {
        if (empty($this->jabatan)) {
            return;
        }

        $jabatanData = Jabatan::find($this->jabatan);
        if (!$jabatanData) {
            return;
        }

        $inisial = $jabatanData->kode_jabatan;
        $year = date('Y');
        
        // Hitung jumlah karyawan dengan jabatan yang sama (exclude current)
        $count = Karyawan::where('jabatan_id', $this->jabatan)
            ->where('id', '!=', $this->karyawan->id)
            ->count();

        $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        $this->id_card = "{$inisial}-{$year}{$number}";
        $this->username = $this->id_card;
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
            // 1. Update User
            if ($this->karyawan->user) {
                $userData = [
                    'username' => $this->username,
                    'status' => $this->status === 'active' ? 'active' : 'inactive',
                ];
                
                // Only update password if it was changed
                if ($this->passwordChanged && $this->generatedPassword !== '******') {
                    $userData['password'] = Hash::make($this->generatedPassword);
                }
                
                $this->karyawan->user->update($userData);
            }

            // 2. Handle foto upload
            $fotoPath = $this->existing_foto;
            if ($this->foto_karyawan) {
                // Delete old foto
                if ($this->existing_foto) {
                    Storage::disk('public')->delete($this->existing_foto);
                }
                $fotoPath = $this->foto_karyawan->store('karyawan/foto', 'public');
            }

            // 3. Get departemen from jabatan
            $jabatanData = Jabatan::find($this->jabatan);
            $departemen_id = $jabatanData ? $jabatanData->departemen_id : null;

            // 4. Update Karyawan
            $this->karyawan->update([
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

            // 5. Update/Create Face Embedding if exists
            if ($this->faceEmbedding && $fotoPath) {
                WajahKaryawan::updateOrCreate(
                    ['karyawan_id' => $this->karyawan->id],
                    [
                        'foto_path' => $fotoPath,
                        'face_embedding' => $this->faceEmbedding,
                    ]
                );
            }

            session()->flash('success', 'Data karyawan berhasil diperbarui!');
            return $this->redirect(route('admin.karyawan.index'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    #[Title('Edit Karyawan')]
    public function render()
    {
        $jabatans = Jabatan::with('departemen')
            ->where('status', 'active')
            ->orderBy('nama_jabatan')
            ->get();

        return view('livewire.admin.karyawan.edit', [
            'jabatans' => $jabatans
        ]);
    }
}
