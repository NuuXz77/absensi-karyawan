<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKerja extends Model
{
    protected $table = 'jadwal_kerja';

    protected $fillable = ['karyawan_id', 'shift_id', 'lokasi_id', 'tanggal', 'status', 'keterangan'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    // Check if karyawan has cuti/izin on this date
    public function getCutiAttribute()
    {
        return Cuti::where('karyawan_id', $this->karyawan_id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $this->tanggal)
            ->whereDate('tanggal_selesai', '>=', $this->tanggal)
            ->first();
    }

    public function getIzinAttribute()
    {
        return Izin::where('karyawan_id', $this->karyawan_id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $this->tanggal)
            ->whereDate('tanggal_selesai', '>=', $this->tanggal)
            ->first();
    }
}
