<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'user_id',
        'id_card',
        'nip',
        'nama_lengkap',
        'tanggal_lahir',
        'foto_karyawan',
        'jabatan',
        'departemen',
        'status',
    ];

    // relasi ke table users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
