# API Documentation - Absensi PWA

Base URL: `http://your-domain.com/api/v1`

## Authentication

API menggunakan Laravel Sanctum untuk authentication. Token harus disertakan di header untuk endpoint yang dilindungi:

```
Authorization: Bearer {token}
```

---

## 🔐 Auth Endpoints

### 1. Login
**POST** `/login`

**Request Body:**
```json
{
  "username": "string",
  "password": "string"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "username": "john_doe",
      "role": "karyawan",
      "status": "aktif",
      "harus_mengganti_password": false
    },
    "karyawan": {
      "id": 1,
      "nip": "K001",
      "nama_lengkap": "John Doe",
      "email": "john@example.com",
      "foto_karyawan": "http://domain.com/storage/karyawan/photo.jpg",
      "no_telepon": "08123456789",
      "jenis_kelamin": "L",
      "jabatan": {
        "id": 1,
        "nama": "Staff IT"
      },
      "departemen": {
        "id": 1,
        "nama": "IT"
      }
    },
    "token": "1|abc123def456..."
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Username atau password salah"
}
```

---

### 2. Logout
**POST** `/logout`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

---

### 3. Get User Info
**GET** `/me`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "karyawan": { ... }
  }
}
```

---

## 👤 Karyawan Endpoints

### 1. Get Profile
**GET** `/karyawan/profile`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "id_card": "1234567890123456",
    "nip": "K001",
    "nama_lengkap": "John Doe",
    "email": "john@example.com",
    "tanggal_lahir": "1990-01-01",
    "foto_karyawan": "http://domain.com/storage/karyawan/photo.jpg",
    "no_telepon": "08123456789",
    "jenis_kelamin": "L",
    "alamat": "Jl. Example No. 123",
    "status": "aktif",
    "jabatan": {
      "id": 1,
      "nama": "Staff IT",
      "deskripsi": "Staff bagian IT"
    },
    "departemen": {
      "id": 1,
      "nama": "IT",
      "deskripsi": "Departemen IT"
    }
  }
}
```

---

### 2. Update Profile
**PUT** `/karyawan/profile`

**Headers:** 
- `Authorization: Bearer {token}`
- `Content-Type: multipart/form-data`

**Request Body (form-data):**
```
email: john@example.com
no_telepon: 08123456789
alamat: Jl. Example No. 123
foto_karyawan: [file]
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Profile berhasil diupdate",
  "data": { ... }
}
```

---

## 📅 Jadwal Endpoints

### 1. Get Jadwal Kerja
**GET** `/jadwal?bulan=12&tahun=2025`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `bulan` (optional): Bulan (1-12), default: bulan sekarang
- `tahun` (optional): Tahun, default: tahun sekarang

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tanggal": "2025-12-30",
      "hari": "Senin",
      "shift": {
        "id": 1,
        "nama": "Shift Pagi",
        "jam_masuk": "08:00:00",
        "jam_pulang": "17:00:00",
        "warna": "#3B82F6"
      },
      "keterangan": null,
      "is_libur": false
    }
  ]
}
```

---

### 2. Get Jadwal Hari Ini
**GET** `/jadwal/today`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "tanggal": "2025-12-30",
    "hari": "Senin",
    "shift": {
      "id": 1,
      "nama": "Shift Pagi",
      "jam_masuk": "08:00:00",
      "jam_pulang": "17:00:00",
      "toleransi_keterlambatan": 15,
      "warna": "#3B82F6"
    },
    "keterangan": null,
    "is_libur": false
  }
}
```

---

### 3. Get All Shifts
**GET** `/jadwal/shifts`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama": "Shift Pagi",
      "jam_masuk": "08:00:00",
      "jam_pulang": "17:00:00",
      "toleransi_keterlambatan": 15,
      "warna": "#3B82F6"
    }
  ]
}
```

---

## 🏖️ Cuti Endpoints

### 1. Get List Cuti
**GET** `/cuti?status=pending`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `status` (optional): pending | disetujui | ditolak

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tanggal_mulai": "2025-12-25",
      "tanggal_selesai": "2025-12-27",
      "jumlah_hari": 3,
      "jenis_cuti": "tahunan",
      "alasan": "Liburan akhir tahun",
      "status": "pending",
      "keterangan_ditolak": null,
      "disetujui_oleh": null,
      "tanggal_disetujui": null,
      "created_at": "2025-12-20 10:00:00"
    }
  ]
}
```

---

### 2. Submit Pengajuan Cuti
**POST** `/cuti`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "tanggal_mulai": "2025-12-25",
  "tanggal_selesai": "2025-12-27",
  "jenis_cuti": "tahunan",
  "alasan": "Liburan akhir tahun"
}
```

**Jenis Cuti:** `tahunan` | `sakit` | `darurat` | `lainnya`

**Success Response (201):**
```json
{
  "success": true,
  "message": "Pengajuan cuti berhasil dikirim",
  "data": {
    "id": 1,
    "tanggal_mulai": "2025-12-25",
    "tanggal_selesai": "2025-12-27",
    "jumlah_hari": 3,
    "jenis_cuti": "tahunan",
    "status": "pending"
  }
}
```

---

### 3. Get Saldo Cuti
**GET** `/cuti/saldo?tahun=2025`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `tahun` (optional): Tahun, default: tahun sekarang

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "tahun": 2025,
    "jatah_cuti": 12,
    "cuti_terpakai": 5,
    "sisa_cuti": 7
  }
}
```

---

## 📝 Izin Endpoints

### 1. Get List Izin
**GET** `/izin?status=pending`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `status` (optional): pending | disetujui | ditolak

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tanggal": "2025-12-30",
      "jenis_izin": "sakit",
      "keterangan": "Demam tinggi",
      "dokumen": "http://domain.com/storage/izin/doc.pdf",
      "status": "pending",
      "keterangan_ditolak": null,
      "disetujui_oleh": null,
      "tanggal_disetujui": null,
      "created_at": "2025-12-30 08:00:00"
    }
  ]
}
```

---

### 2. Submit Pengajuan Izin
**POST** `/izin`

**Headers:** 
- `Authorization: Bearer {token}`
- `Content-Type: multipart/form-data`

**Request Body (form-data):**
```
tanggal: 2025-12-30
jenis_izin: sakit
keterangan: Demam tinggi
dokumen: [file] (optional)
```

**Jenis Izin:** `sakit` | `keperluan_pribadi` | `dinas_luar` | `lainnya`

**Success Response (201):**
```json
{
  "success": true,
  "message": "Pengajuan izin berhasil dikirim",
  "data": {
    "id": 1,
    "tanggal": "2025-12-30",
    "jenis_izin": "sakit",
    "status": "pending"
  }
}
```

---

## 🕐 Absensi Endpoints

### 1. Get List Absensi
**GET** `/absensi?bulan=12&tahun=2025`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `bulan` (optional): Bulan (1-12), default: bulan sekarang
- `tahun` (optional): Tahun, default: tahun sekarang

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tanggal": "2025-12-30",
      "waktu_masuk": "08:00:00",
      "waktu_keluar": "17:00:00",
      "status": "hadir",
      "keterangan": null,
      "lokasi": {
        "id": 1,
        "nama": "Kantor Pusat"
      }
    }
  ]
}
```

---

### 2. Get Absensi Hari Ini
**GET** `/absensi/today`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "tanggal": "2025-12-30",
    "waktu_masuk": "08:00:00",
    "waktu_keluar": null,
    "status": "hadir",
    "keterangan": null,
    "lokasi": {
      "id": 1,
      "nama": "Kantor Pusat"
    }
  }
}
```

**Jika belum absen:**
```json
{
  "success": false,
  "message": "Belum ada absensi hari ini",
  "data": null
}
```

---

### 3. Clock In (Absen Masuk)
**POST** `/absensi/clock-in`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "lokasi_id": 1,
  "latitude": -6.200000,
  "longitude": 106.816666
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Absen masuk berhasil",
  "data": {
    "id": 1,
    "tanggal": "2025-12-30",
    "waktu_masuk": "08:00:00",
    "status": "hadir"
  }
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Anda berada di luar radius lokasi absensi"
}
```

---

### 4. Clock Out (Absen Keluar)
**POST** `/absensi/clock-out`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "latitude": -6.200000,
  "longitude": 106.816666
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Absen keluar berhasil",
  "data": {
    "id": 1,
    "tanggal": "2025-12-30",
    "waktu_masuk": "08:00:00",
    "waktu_keluar": "17:00:00",
    "status": "hadir"
  }
}
```

---

### 5. Get Available Locations
**GET** `/absensi/locations`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nama": "Kantor Pusat",
      "latitude": -6.200000,
      "longitude": 106.816666,
      "radius_meter": 100
    }
  ]
}
```

---

### 6. Get Absensi Statistics
**GET** `/absensi/statistics?bulan=12&tahun=2025`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `bulan` (optional): Bulan (1-12), default: bulan sekarang
- `tahun` (optional): Tahun, default: tahun sekarang

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "bulan": 12,
    "tahun": 2025,
    "total_hadir": 20,
    "total_terlambat": 3,
    "total_alpha": 1
  }
}
```

---

## 🔴 Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Terjadi kesalahan: ..."
}
```

---

## 📱 Framework7 Integration Example

```javascript
// Login
framework7.request({
  url: 'http://your-domain.com/api/v1/login',
  method: 'POST',
  dataType: 'json',
  data: {
    username: 'john_doe',
    password: 'password123'
  },
  success: function(data) {
    // Save token
    localStorage.setItem('token', data.data.token);
    localStorage.setItem('user', JSON.stringify(data.data.user));
  }
});

// Authenticated Request
framework7.request({
  url: 'http://your-domain.com/api/v1/karyawan/profile',
  method: 'GET',
  dataType: 'json',
  headers: {
    'Authorization': 'Bearer ' + localStorage.getItem('token')
  },
  success: function(data) {
    console.log(data.data);
  }
});
```

---

## ⚙️ Setup Instructions

1. Install Laravel Sanctum (jika belum):
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

2. Tambahkan Sanctum middleware di `app/Http/Kernel.php`:
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

3. Configure CORS di `config/cors.php` untuk PWA domain.

4. Test API menggunakan Postman atau Thunder Client.
