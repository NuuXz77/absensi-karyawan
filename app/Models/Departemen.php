<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';

    protected $fillable = [
        'nama_departemen',
        'kode_departemen',
        'status',
    ];

    // relasi ke table jabatans
    public function jabatans()
    {
        return $this->hasMany(Jabatan::class);
    }

    // relasi ke table karyawan (string-based)
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'departemen', 'nama_departemen');
    }
}
