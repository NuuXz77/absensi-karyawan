<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoCuti extends Model
{
    protected $table = 'saldo_cuti';
    protected $fillable = [
        'karyawan_id',
        'tahun',
        'jumlah_cuti',
        'sisa_cuti',
    ];

    // relasi ke table karyawans
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
