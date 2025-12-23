<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $fillable = [
        'karyawan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'status',
        'disetujui_oleh',
    ];
    
    // relasi ke table karyawans
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // relasi ke table users (yang menyetujui)
    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
