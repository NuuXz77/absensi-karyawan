# Profile Modals

Folder ini berisi modal-modal terpisah untuk halaman profile karyawan.

## Struktur File

### Blade View Files
- `informasi-akun.blade.php` - Modal untuk menampilkan informasi akun user
- `data-pribadi.blade.php` - Modal untuk menampilkan data pribadi karyawan
- `foto-identitas.blade.php` - Modal untuk menampilkan foto profil dan ID card
- `keamanan-akun.blade.php` - Modal untuk mengubah password dan informasi keamanan
- `informasi-sistem.blade.php` - Modal untuk informasi sistem aplikasi

### Livewire Component Files (di app/Livewire/Karyawan/Profile/Modals/)
- `InformasiAkun.php`
- `DataPribadi.php`
- `FotoIdentitas.php`
- `KeamananAkun.php` - **Dengan fitur ubah password**
- `InformasiSistem.php`

## Fitur

✅ **Modular & Scalable** - Setiap modal adalah component terpisah
✅ **Responsive Design** - Grid layout yang responsive untuk mobile & desktop
✅ **Icon Integration** - Menggunakan Heroicons untuk konsistensi
✅ **DaisyUI Styling** - Menggunakan komponen DaisyUI
✅ **Livewire Integration** - Event dispatcher untuk membuka/menutup modal
✅ **Form Validation** - Validasi lengkap untuk form ubah password
✅ **Security** - Hash password dengan bcrypt, validasi password lama

## Fitur Ubah Password

Modal `keamanan-akun.blade.php` memiliki fitur lengkap untuk ubah password:

### Field Input
1. **Password Lama** - Untuk validasi identitas user
2. **Password Baru** - Minimal 8 karakter
3. **Konfirmasi Password** - Harus sama dengan password baru

### Validasi
- Password lama harus sesuai
- Password baru minimal 8 karakter
- Password baru harus berbeda dengan password lama
- Konfirmasi password harus cocok

### Proses Update
- Hash password dengan bcrypt
- Set `harus_mengganti_password = 0` setelah berhasil ubah password
- Tampilkan toast notification sukses/error
- Auto clear form setelah sukses

### Status Password
- Jika `harus_mengganti_password = 1`: Tampilkan alert warning (disarankan ubah password)
- Jika `harus_mengganti_password = 0`: Tampilkan alert success (password aman)

## Cara Penggunaan

Untuk membuka modal dari komponen induk:

```php
// Di Livewire Component
public function openModal($modalType)
{
    $this->dispatch('open-' . $modalType . '-modal');
}
```

```blade
<!-- Di Blade Template -->
<button wire:click="openModal('keamanan-akun')">
    Ubah Password
</button>
```

### Contoh Update Password

```php
// Di KeamananAkun Component
public function updatePassword()
{
    $this->validate([
        'password_lama' => 'required',
        'password_baru' => ['required', 'min:8', 'confirmed'],
    ]);

    if (!Hash::check($this->password_lama, $this->user->password)) {
        $this->showError = true;
        return;
    }

    $this->user->update([
        'password' => Hash::make($this->password_baru),
        'harus_mengganti_password' => 0,
    ]);
}
```

## Style Guide

Setiap modal mengikuti pattern yang konsisten:

1. **Header** - Title dengan icon
2. **Content** - Grid layout dengan fieldset dan legend
3. **Footer** - Tombol close/action
4. **Icons** - Heroicons untuk setiap field
5. **Responsive** - col-span-2 sm:col-span-1 untuk responsiveness

## Maintenance

Untuk menambah modal baru:

1. Buat file blade di folder ini
2. Buat Livewire component di `app/Livewire/Karyawan/Profile/Modals/`
3. Tambahkan @livewire directive di index.blade.php
4. Implementasikan event listener untuk open/close modal
