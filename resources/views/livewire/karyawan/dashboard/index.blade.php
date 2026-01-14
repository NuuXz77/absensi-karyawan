<div wire:poll.5s>
    <div class="space-y-4">
        
        {{-- Status Hari Ini --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    Status Hari Ini
                </h2>
                
                <div class="grid grid-cols-2 gap-4 mt-4">
                    {{-- Sudah/Belum Absen --}}
                    @if($absensiHariIni && $absensiHariIni->jam_masuk)
                        <div class="text-center p-4 bg-success/20 rounded-lg">
                            <x-heroicon-o-check-circle class="w-12 h-12 mx-auto mb-2 text-success" />
                            <div class="font-bold text-sm text-success">Sudah Absen</div>
                            <div class="text-xs text-base-content mt-1">{{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }} WIB</div>
                        </div>
                    @else
                        <div class="text-center p-4 bg-error/20 rounded-lg">
                            <x-heroicon-o-x-circle class="w-12 h-12 mx-auto mb-2 text-error" />
                            <div class="font-bold text-sm text-error">Belum Absen</div>
                            <div class="text-xs text-base-content mt-1">-</div>
                        </div>
                    @endif
                    
                    {{-- Jam Masuk Seharusnya --}}
                    @if($jadwalHariIni && $jadwalHariIni->shift)
                        <div class="text-center p-4 bg-info/20 rounded-lg">
                            <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-2 text-info" />
                            <div class="font-bold text-sm text-info">Jam Masuk</div>
                            <div class="text-xs text-base-content mt-1">{{ \Carbon\Carbon::parse($jadwalHariIni->shift->jam_masuk)->format('H:i') }} WIB</div>
                        </div>
                    @else
                        <div class="text-center p-4 bg-base-200 rounded-lg">
                            <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-2 text-base-content/50" />
                            <div class="font-bold text-sm text-base-content/50">Tidak Ada Jadwal</div>
                            <div class="text-xs text-base-content mt-1">-</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Shift & Jadwal Hari Ini --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                    Shift & Jadwal Hari Ini
                </h2>
                
                @if($jadwalHariIni)
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                            <div>
                                <div class="font-bold">{{ $jadwalHariIni->shift->nama_shift ?? 'Shift' }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($jadwalHariIni->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</div>
                            </div>
                            <div class="text-right">
                                @if($jadwalHariIni->shift)
                                    <div class="badge badge-primary">
                                        {{ \Carbon\Carbon::parse($jadwalHariIni->shift->jam_masuk)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($jadwalHariIni->shift->jam_pulang)->format('H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($jadwalHariIni->lokasi)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="font-semibold">Lokasi:</span>
                                <span class="text-gray-600">{{ $jadwalHariIni->lokasi->nama_lokasi }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-4 text-center text-gray-500 py-8">
                        <x-heroicon-o-calendar-days class="w-16 h-16 mx-auto mb-2 opacity-50" />
                        <p>Tidak ada jadwal untuk hari ini</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Total Keterlambatan Bulan Ini --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning" />
                    Total Keterlambatan Bulan Ini
                </h2>
                
                <div class="mt-4">
                    <div class="stats shadow w-full">
                        <div class="stat">
                            <div class="stat-figure text-warning">
                                <x-heroicon-o-clock class="w-8 h-8" />
                            </div>
                            <div class="stat-title">{{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</div>
                            <div class="stat-value text-warning">{{ $totalKeterlambatan }} Kali</div>
                            <div class="stat-desc">Total: {{ $totalMenitTerlambat }} menit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Absen Bulan Berjalan --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                    Ringkasan Absen {{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}
                </h2>
                
                <div class="grid grid-cols-2 gap-3 mt-4">
                    {{-- Hadir --}}
                    <div class="stat bg-success/20 rounded-lg p-3">
                        <div class="stat-title text-xs">Hadir</div>
                        <div class="stat-value text-2xl text-success">{{ $absensiHadir }}</div>
                        <div class="stat-desc">Hari</div>
                    </div>
                    
                    {{-- Izin --}}
                    <div class="stat bg-info/20 rounded-lg p-3">
                        <div class="stat-title text-xs">Izin</div>
                        <div class="stat-value text-2xl text-info">{{ $izinBulanIni }}</div>
                        <div class="stat-desc">Hari</div>
                    </div>
                    
                    {{-- Cuti/Sakit --}}
                    <div class="stat bg-warning/20 rounded-lg p-3">
                        <div class="stat-title text-xs">Cuti</div>
                        <div class="stat-value text-2xl text-warning">{{ $cutiBulanIni }}</div>
                        <div class="stat-desc">Hari</div>
                    </div>
                    
                    {{-- Alpha --}}
                    <div class="stat bg-error/20 rounded-lg p-3">
                        <div class="stat-title text-xs">Alpha</div>
                        <div class="stat-value text-2xl text-error">{{ $alphaBulanIni }}</div>
                        <div class="stat-desc">Hari</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifikasi --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <x-heroicon-o-bell class="w-5 h-5" />
                    Notifikasi
                </h2>
                
                <div class="mt-4 space-y-3">
                    @php
                        $hasNotifications = false;
                    @endphp
                    
                    {{-- Notifikasi Izin --}}
                    @foreach($notifikasiIzin as $izin)
                        @php $hasNotifications = true; @endphp
                        <div class="alert {{ $izin->status == 'disetujui' ? 'alert-success' : 'alert-error' }} shadow-sm">
                            @if($izin->status == 'disetujui')
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                            @else
                                <x-heroicon-o-x-circle class="w-5 h-5" />
                            @endif
                            <div class="flex-1">
                                <h3 class="font-bold text-sm">Pengajuan Izin {{ ucfirst($izin->status) }}</h3>
                                <div class="text-xs">Izin tanggal {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }} telah {{ $izin->status }}</div>
                            </div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($izin->updated_at)->diffForHumans() }}</div>
                        </div>
                    @endforeach
                    
                    {{-- Notifikasi Cuti --}}
                    @foreach($notifikasiCuti as $cuti)
                        @php $hasNotifications = true; @endphp
                        <div class="alert {{ $cuti->status == 'disetujui' ? 'alert-success' : 'alert-error' }} shadow-sm">
                            @if($cuti->status == 'disetujui')
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                            @else
                                <x-heroicon-o-x-circle class="w-5 h-5" />
                            @endif
                            <div class="flex-1">
                                <h3 class="font-bold text-sm">Pengajuan Cuti {{ ucfirst($cuti->status) }}</h3>
                                <div class="text-xs">Cuti tanggal {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} telah {{ $cuti->status }}</div>
                            </div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cuti->updated_at)->diffForHumans() }}</div>
                        </div>
                    @endforeach
                    
                    {{-- Notifikasi Terlambat Hari Ini --}}
                    @if($absensiHariIni && $absensiHariIni->status == 'terlambat')
                        @php $hasNotifications = true; @endphp
                        <div class="alert alert-warning shadow-sm">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            <div class="flex-1">
                                <h3 class="font-bold text-sm">Terlambat Hari Ini</h3>
                                @if($jadwalHariIni && $jadwalHariIni->shift)
                                    @php
                                        $jamMasukSeharusnya = \Carbon\Carbon::parse($absensiHariIni->tanggal . ' ' . $jadwalHariIni->shift->jam_masuk);
                                        $jamMasukAktual = \Carbon\Carbon::parse($absensiHariIni->tanggal . ' ' . $absensiHariIni->jam_masuk);
                                        $menitTerlambat = $jamMasukAktual->diffInMinutes($jamMasukSeharusnya);
                                    @endphp
                                    <div class="text-xs">Anda terlambat {{ $menitTerlambat }} menit hari ini</div>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">Hari ini</div>
                        </div>
                    @endif
                    
                    {{-- Notifikasi Lupa Absen Pulang Kemarin --}}
                    @if($absensiKemarin && !$absensiKemarin->jam_pulang)
                        @php $hasNotifications = true; @endphp
                        <div class="alert alert-error shadow-sm">
                            <x-heroicon-o-x-circle class="w-5 h-5" />
                            <div class="flex-1">
                                <h3 class="font-bold text-sm">Lupa Absen Pulang</h3>
                                <div class="text-xs">Anda lupa absen pulang kemarin ({{ \Carbon\Carbon::parse($absensiKemarin->tanggal)->format('d M') }})</div>
                            </div>
                            <div class="text-xs text-gray-500">Kemarin</div>
                        </div>
                    @endif
                    
                    {{-- Jika tidak ada notifikasi --}}
                    @if(!$hasNotifications)
                        <div class="text-center text-gray-500 py-8">
                            <x-heroicon-o-bell-slash class="w-16 h-16 mx-auto mb-2 opacity-50" />
                            <p>Tidak ada notifikasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
