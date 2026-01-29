<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoCutiDanIzin extends Model
{
    protected $table = 'saldo_cuti_dan_izin';
    protected $fillable = [
        'karyawan_id',
        'tahun',
        'total_izin',
        'sisa_izin',
        'total_cuti',
        'sisa_cuti',
    ];

    protected $appends = ['cuti_terpakai', 'izin_terpakai'];

    // relasi ke table karyawans
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Hitung jumlah cuti yang sudah terpakai
    public function getCutiTerpakaiAttribute()
    {
        return $this->total_cuti - $this->sisa_cuti;
    }

    // Hitung jumlah izin yang sudah terpakai
    public function getIzinTerpakaiAttribute()
    {
        return $this->total_izin - $this->sisa_izin;
    }
}
