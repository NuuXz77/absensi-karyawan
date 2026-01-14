<?php

namespace App\Livewire\Admin\Karyawan;

use Livewire\Component;
use App\Models\Karyawan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
class Detail extends Component
{
    public Karyawan $karyawan;

    public function mount($id)
    {
        $this->karyawan = Karyawan::with(['user', 'jabatan.departemen', 'departemen', 'wajah'])
            ->withCount(['absensi', 'izin', 'cuti'])
            ->findOrFail($id);
    }

    public function copyDetailToClipboard()
    {
        $data = $this->generateDetailText();
        
        // Copy to clipboard using JavaScript - skipRender untuk mencegah re-render
        $this->js("
            const text = `{$this->escapeForJs($data)}`;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    console.log('✓ Text copied to clipboard');
                    showToast();
                }).catch(err => {
                    console.error('Failed to copy:', err);
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
            
            function fallbackCopy(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.top = '-9999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    console.log('✓ Text copied using fallback');
                    showToast();
                } catch (err) {
                    console.error('✗ Copy failed:', err);
                }
                document.body.removeChild(textArea);
            }
            
            function showToast() {
                // Buat toast element secara dinamis
                const toastContainer = document.querySelector('.toast');
                const toastDiv = document.createElement('div');
                toastDiv.className = 'alert alert-success flex flex-row items-center';
                toastDiv.innerHTML = `
                    <svg class=\"w-5 h-5\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path>
                    </svg>
                    <span>Data karyawan berhasil disalin ke clipboard!</span>
                `;
                
                toastContainer.appendChild(toastDiv);
                
                // Animasi masuk
                setTimeout(() => {
                    toastDiv.style.opacity = '1';
                    toastDiv.style.transform = 'translateY(0)';
                }, 10);
                
                // Auto hide setelah 3 detik
                setTimeout(() => {
                    toastDiv.style.opacity = '0';
                    toastDiv.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        toastDiv.remove();
                    }, 300);
                }, 3000);
            }
        ");
        
        $this->skipRender();
    }

    private function escapeForJs($text)
    {
        return str_replace(
            ['\\', '`', "\n", "\r"],
            ['\\\\', '\\`', '\\n', '\\r'],
            $text
        );
    }

    private function generateDetailText()
    {
        $jenisKelamin = $this->karyawan->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        $status = $this->karyawan->status === 'active' ? 'Aktif' : 'Tidak Aktif';
        $statusAkun = ucfirst($this->karyawan->user->status ?? 'Active');
        $tanggalLahir = \Carbon\Carbon::parse($this->karyawan->tanggal_lahir)->format('d F Y');
        $tanggalBergabung = \Carbon\Carbon::parse($this->karyawan->created_at)->format('d F Y');
        $lamaBekerja = \Carbon\Carbon::parse($this->karyawan->created_at)->diffForHumans();
        
        $text = "═══════════════════════════════════════════\n";
        $text .= "           INFORMASI KARYAWAN\n";
        $text .= "═══════════════════════════════════════════\n\n";
        
        $text .= "📋 DATA PRIBADI\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• NIP             : {$this->karyawan->nip}\n";
        $text .= "• ID Card         : {$this->karyawan->id_card}\n";
        $text .= "• Nama Lengkap    : {$this->karyawan->nama_lengkap}\n";
        $text .= "• Tanggal Lahir   : {$tanggalLahir}\n";
        $text .= "• Jenis Kelamin   : {$jenisKelamin}\n";
        $text .= "• Status Karyawan : {$status}\n\n";
        
        $text .= "📞 INFORMASI KONTAK\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• Email           : {$this->karyawan->email}\n";
        $text .= "• No. Telepon     : {$this->karyawan->no_telepon}\n";
        $text .= "• Alamat          : " . ($this->karyawan->alamat ?? '-') . "\n\n";
        
        $text .= "💼 INFORMASI PEKERJAAN\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• Jabatan         : " . ($this->karyawan->jabatan->nama_jabatan ?? '-') . "\n";
        $text .= "• Departemen      : " . ($this->karyawan->departemen->nama_departemen ?? '-') . "\n";
        $text .= "• Bergabung Sejak : {$tanggalBergabung}\n";
        $text .= "• Lama Bekerja    : {$lamaBekerja}\n\n";
        
        $text .= "👤 INFORMASI AKUN LOGIN\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• Username        : " . ($this->karyawan->user->username ?? '-') . "\n";
        $text .= "• Role            : " . ucfirst($this->karyawan->user->role ?? 'Karyawan') . "\n";
        $text .= "• Status Akun     : {$statusAkun}\n\n";
        
        $text .= "📊 STATISTIK KEHADIRAN\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• Total Absensi   : {$this->karyawan->absensi_count} hari\n";
        $text .= "• Total Izin      : {$this->karyawan->izin_count} kali\n";
        $text .= "• Total Cuti      : {$this->karyawan->cuti_count} hari\n\n";
        
        $faceRecognition = $this->karyawan->wajah ? '✓ Aktif' : '✗ Tidak Aktif';
        $text .= "🔐 KEAMANAN\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "• Face Recognition: {$faceRecognition}\n\n";
        
        $text .= "═══════════════════════════════════════════\n";
        $text .= "Data digenerate pada: " . now()->format('d F Y, H:i:s') . "\n";
        $text .= "═══════════════════════════════════════════\n";
        
        return $text;
    }

    #[Title('Detail Karyawan')]
    public function render()
    {
        return view('livewire.admin.karyawan.detail');
    }
}
