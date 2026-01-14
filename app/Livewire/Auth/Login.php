<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

#[Layout('components.layouts.guest')] 
class Login extends Component
{
    public $username;
    public $password;
    public $remember = false;
    public $showSuccess = false;
    public $showError = false;

    #[Title('Login Page')] 
    public function render()
    {
        return view('livewire.auth.login');
    }

    public function login()
    {
        // Reset toast state setiap kali login dipanggil
        $this->showSuccess = false;
        $this->showError = false;

        // Validasi input
        $this->validate([
            'username' => 'required|string|min:3|max:30',
            'password' => 'required|string|min:3',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.min' => 'Username minimal 3 karakter',
            'username.max' => 'Username maksimal 30 karakter',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 3 karakter',
        ]);

        // Coba melakukan autentikasi
        $credentials = [
            'username' => $this->username,
            'password' => $this->password,
            'status' => 'active', // Hanya user dengan status active yang bisa login
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            // Regenerate session untuk keamanan
            Session::regenerate();

            // Ambil user yang sedang login
            $user = Auth::user();

            // Update last login time
            $user->update(['last_login_at' => now()]);

            // Tampilkan toast success
            $this->showSuccess = true;
            $this->showError = false;

            // Redirect berdasarkan role dengan delay untuk menampilkan toast
            $this->dispatch('login-success');
            
            if ($user->role === 'admin') {
                return $this->redirect('/admin/dashboard', navigate: true);
            } elseif ($user->role === 'karyawan') {
                return $this->redirect('/dashboard', navigate: true);
            }

            // Fallback jika role tidak dikenali
            Auth::logout();
            $this->showError = true;
            $this->showSuccess = false;
            return;
        }

        // Jika login gagal
        $this->showError = true;
        $this->showSuccess = false;
    }
}
