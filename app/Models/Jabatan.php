<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';

    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
        'departemen_id',
        'deskripsi',
        'level',
        'status',
    ];

    // relasi ke table departemens
    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    // relasi ke table karyawan (string-based)
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'jabatan', 'nama_jabatan');
    }
}
