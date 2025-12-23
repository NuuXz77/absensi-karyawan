<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $fillable = ['user_id', 'id_card', 'nip', 'nama_lengkap', 'email', 'tanggal_lahir', 'foto_karyawan', 'no_telepon', 'jenis_kelamin', 'jabatan', 'departemen', 'alamat', 'status'];

    // relasi ke table users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wajah()
    {
        return $this->hasOne(Wajah::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function izin()
    {
        return $this->hasMany(Izin::class);
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function saldoCuti()
    {
        return $this->hasMany(SaldoCuti::class);
    }

    public function jadwalKerja()
    {
        return $this->hasMany(JadwalKerja::class);
    }
}
