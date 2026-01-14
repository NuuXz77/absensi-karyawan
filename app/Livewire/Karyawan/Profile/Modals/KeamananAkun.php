<?php

namespace App\Livewire\Karyawan\Profile\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class KeamananAkun extends Component
{
    public $user;
    public $password_lama = '';
    public $password_baru = '';
    public $password_baru_confirmation = '';
    
    public $showSuccess = false;
    public $showError = false;
    public $errorMessage = '';

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function updatePassword()
    {
        // Validasi input
        $this->validate([
            'password_lama' => 'required',
            'password_baru' => ['required', 'min:8', 'confirmed', 'different:password_lama'],
            'password_baru_confirmation' => 'required',
        ], [
            'password_lama.required' => 'Password lama wajib diisi',
            'password_baru.required' => 'Password baru wajib diisi',
            'password_baru.min' => 'Password baru minimal 8 karakter',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok',
            'password_baru.different' => 'Password baru harus berbeda dengan password lama',
            'password_baru_confirmation.required' => 'Konfirmasi password wajib diisi',
        ]);

        // Cek apakah password lama benar
        if (!Hash::check($this->password_lama, $this->user->password)) {
            $this->showError = true;
            $this->errorMessage = 'Password lama tidak sesuai!';
            return;
        }

        // Update password dan set harus_mengganti_password = 0
        $this->user->update([
            'password' => Hash::make($this->password_baru),
            'harus_mengganti_password' => 0,
        ]);

        // Reset form
        $this->reset(['password_lama', 'password_baru', 'password_baru_confirmation']);
        
        // Tampilkan pesan sukses
        $this->showSuccess = true;
        $this->showError = false;
        
        // Refresh user data
        $this->user = Auth::user();
        
        // Auto hide success message after 3 seconds
        $this->dispatch('password-updated');
    }

    public function closeModal()
    {
        // Reset form dan pesan saat modal ditutup
        $this->reset(['password_lama', 'password_baru', 'password_baru_confirmation', 'showSuccess', 'showError', 'errorMessage']);
        $this->dispatch('close-keamanan-akun-modal');
    }

    public function render()
    {
        return view('livewire.karyawan.profile.modals.keamanan-akun');
    }
}
