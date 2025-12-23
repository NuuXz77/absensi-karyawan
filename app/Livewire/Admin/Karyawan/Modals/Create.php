<?php

namespace App\Livewire\Admin\Karyawan\Modals;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\WajahKaryawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $showModal = false;

    // User fields
    public $username = '';
    public $email = '';
    public $generatedPassword = '';

    // Karyawan fields
    public $nip = '';
    public $id_card = '';
    public $nama_lengkap = '';
    public $tanggal_lahir = '';
    public $foto_karyawan = null;
    public $jabatan = '';
    public $departemen = '';
    public $status = 'aktif';

    protected function rules()
    {
        return [
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|string|unique:karyawan,nip',
            'id_card' => 'required|string|size:16|unique:karyawan,id_card',
            'nama_lengkap' => 'required|string|min:3|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'foto_karyawan' => 'nullable|image|max:2048', // 2MB max
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif,cuti',
        ];
    }

    protected $messages = [
        'username.required' => 'Username wajib diisi',
        'username.unique' => 'Username sudah digunakan',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah digunakan',
        'nip.required' => 'NIP wajib diisi',
        'nip.unique' => 'NIP sudah digunakan',
        'id_card.required' => 'NIK wajib diisi',
        'id_card.size' => 'NIK harus 16 digit',
        'id_card.unique' => 'NIK sudah terdaftar',
        'nama_lengkap.required' => 'Nama lengkap wajib diisi',
        'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
        'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
        'foto_karyawan.image' => 'File harus berupa gambar',
        'foto_karyawan.max' => 'Ukuran foto maksimal 2MB',
        'jabatan.required' => 'Jabatan wajib diisi',
        'departemen.required' => 'Departemen wajib diisi',
    ];

    public function mount()
    {
        $this->showModal = true;
        $this->status = 'aktif';
        $this->generatePassword();
    }

    public function generatePassword()
    {
        // Generate random password with letters, numbers and special characters
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*';
        
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Shuffle the password
        $password = str_shuffle($password);
        
        $this->generatedPassword = $password;
    }

    public function copyPassword()
    {
        $this->dispatch('password-copied', [
            'message' => 'Password berhasil disalin ke clipboard!'
        ]);
    }

    public function removeFoto()
    {
        $this->foto_karyawan = null;
    }

    public function closeModal()
    {
        $this->reset();
        $this->showModal = false;
        $this->dispatch('modal-closed');
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // 1. Create User Account
            $user = User::create([
                'username' => $this->username,
                'email' => $this->email,
                'password' => Hash::make($this->generatedPassword),
                'role' => 'karyawan',
                'harus_mengganti_password' => true,
                'status' => 'active',
            ]);

            // 2. Handle Photo Upload
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
                'tanggal_lahir' => $this->tanggal_lahir,
                'foto_karyawan' => $fotoPath,
                'jabatan' => $this->jabatan,
                'departemen' => $this->departemen,
                'status' => $this->status,
            ]);

            // 4. Process Face Recognition (if photo exists)
            if ($fotoPath) {
                $this->processFaceRecognition($karyawan, $fotoPath);
            }

            DB::commit();

            // Success notification
            $this->dispatch('karyawan-created', [
                'message' => 'Karyawan berhasil ditambahkan!',
                'hasPhoto' => $fotoPath ? true : false
            ]);

            $this->closeModal();
            $this->reset();

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded file if exists
            if (isset($fotoPath) && $fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }

            $this->dispatch('error', [
                'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process face recognition using Python service
     */
    private function processFaceRecognition($karyawan, $fotoPath)
    {
        try {
            $fullPath = Storage::disk('public')->path($fotoPath);

            // TODO: Call Python face recognition service
            // This is a placeholder for the actual implementation
            // You'll need to set up a Python service/API that processes the image
            
            /*
            Example structure:
            
            $response = Http::timeout(30)->post('http://localhost:5000/api/process-face', [
                'image_path' => $fullPath,
                'karyawan_id' => $karyawan->id,
            ]);

            if ($response->successful()) {
                $embedding = $response->json('embedding');
                
                WajahKaryawan::create([
                    'karyawan_id' => $karyawan->id,
                    'embedding' => json_encode($embedding),
                    'model_version' => $response->json('model_version', '1.0'),
                ]);
            }
            */

            // For now, just create a placeholder record
            WajahKaryawan::create([
                'karyawan_id' => $karyawan->id,
                'embedding' => json_encode([]), // Will be filled by Python service
                'model_version' => '1.0',
            ]);

        } catch (\Exception $e) {
            // Log error but don't fail the transaction
            logger()->error('Face recognition processing failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.karyawan.modals.create');
    }
}
