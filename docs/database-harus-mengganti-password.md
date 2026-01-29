# Database Schema - Users Table

## Kolom Terkait Password Security

### `harus_mengganti_password` (boolean)

Kolom ini digunakan untuk menandai apakah user perlu mengganti password mereka.

**Nilai:**
- `1` (true) - User disarankan/diwajibkan untuk mengganti password
- `0` (false) - Password sudah aman, tidak perlu diganti

**Default Value:** `true` (1)

**Penggunaan:**

1. **Saat User Baru Dibuat**
   ```php
   User::create([
       'username' => 'karyawan01',
       'password' => Hash::make('default123'),
       'harus_mengganti_password' => 1, // Wajib ubah password
   ]);
   ```

2. **Setelah Admin Reset Password**
   ```php
   $user->update([
       'password' => Hash::make('newpassword123'),
       'harus_mengganti_password' => 1, // User harus ubah lagi
   ]);
   ```

3. **Setelah User Ubah Password Sendiri**
   ```php
   $user->update([
       'password' => Hash::make($newPassword),
       'harus_mengganti_password' => 0, // Password sudah aman
   ]);
   ```

**UI Behavior:**

- Jika `harus_mengganti_password = 1`:
  - Tampilkan alert warning di modal keamanan akun
  - Pesan: "Anda disarankan untuk mengganti password Anda demi keamanan akun"
  - Badge/Icon: Warning (kuning)

- Jika `harus_mengganti_password = 0`:
  - Tampilkan alert success di modal keamanan akun
  - Pesan: "Password Anda sudah diperbarui. Anda dapat mengubahnya kapan saja"
  - Badge/Icon: Success (hijau)

**Security Best Practices:**

1. Set ke `1` setiap kali admin reset password user
2. Set ke `0` hanya setelah user berhasil ubah password sendiri
3. Tampilkan reminder di dashboard jika nilai = `1`
4. (Optional) Paksa user ubah password di login pertama kali

## Migration

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('username')->unique();
    $table->string('password');
    $table->enum('role', ['admin', 'karyawan'])->default('karyawan');
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->boolean('harus_mengganti_password')->default(true); // ← Kolom ini
    $table->timestamp('last_login_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

## Model Fillable

Pastikan kolom ada di `$fillable`:

```php
protected $fillable = [
    'username',
    'password',
    'role',
    'status',
    'harus_mengganti_password', // ← Harus ada di sini
    'last_login_at',
];
```

## Related Files

- Migration: `database/migrations/0001_01_01_000000_create_users_table.php`
- Model: `app/Models/User.php`
- Component: `app/Livewire/Karyawan/Profile/Modals/KeamananAkun.php`
- View: `resources/views/livewire/karyawan/profile/modals/keamanan-akun.blade.php`
