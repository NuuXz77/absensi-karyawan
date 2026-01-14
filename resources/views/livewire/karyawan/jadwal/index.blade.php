<div>
    <div class="space-y-4">
        
        {{-- SECTION 1 — Jadwal Hari Ini (Primary Focus) --}}
        <div class="card bg-base-100 border border-base-300 shadow-lg">
            <div class="card-body p-4 md:p-6">
                <h2 class="card-title text-base md:text-lg mb-3 md:mb-4">
                    <x-heroicon-o-calendar-days class="w-4 h-4 md:w-5 md:h-5" />
                    Jadwal Hari Ini
                </h2>
                
                @if($jadwalHariIni)
                    <div class="space-y-3 md:space-y-4">
                        {{-- Tanggal & Status --}}
                        <div class="flex items-center justify-between p-3 md:p-4 bg-primary/5 rounded-xl border border-primary/20">
                            <div>
                                <div class="text-xs md:text-sm text-base-content/70">Tanggal</div>
                                <div class="text-sm md:text-lg font-bold text-primary">
                                    {{ Carbon\Carbon::today()->isoFormat('dddd, D MMMM YYYY') }}
                                </div>
                            </div>
                            <div>
                                @if($cutiHariIni)
                                    <div class="badge badge-warning gap-1 text-xs md:badge-md">
                                        <x-heroicon-o-calendar-days class="w-3 h-3 md:w-4 md:h-4" />
                                        Cuti
                                    </div>
                                @elseif($izinHariIni)
                                    <div class="badge badge-error gap-1 text-xs md:badge-md">
                                        <x-heroicon-o-exclamation-circle class="w-3 h-3 md:w-4 md:h-4" />
                                        Izin
                                    </div>
                                @elseif(Carbon\Carbon::today()->isWeekend())
                                    <div class="badge badge-error gap-1 text-xs md:badge-md">
                                        <x-heroicon-o-calendar class="w-3 h-3 md:w-4 md:h-4" />
                                        Libur
                                    </div>
                                @else
                                    <div class="badge badge-success gap-1 text-xs md:badge-md">
                                        <x-heroicon-o-briefcase class="w-3 h-3 md:w-4 md:h-4" />
                                        Kerja
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Detail Shift --}}
                        <div class="grid grid-cols-2 gap-2 md:gap-3">
                            {{-- Nama Shift --}}
                            <div class="p-3 md:p-4 bg-base-200 rounded-lg">
                                <div class="text-xs text-base-content/70 mb-1">Shift</div>
                                <div class="text-sm md:text-base font-bold">{{ $jadwalHariIni->shift->nama_shift }}</div>
                            </div>
                            
                            {{-- Durasi Kerja --}}
                            <div class="p-3 md:p-4 bg-base-200 rounded-lg">
                                <div class="text-xs text-base-content/70 mb-1">Durasi</div>
                                @php
                                    $jamMasuk = Carbon\Carbon::parse($jadwalHariIni->shift->jam_masuk);
                                    $jamPulang = Carbon\Carbon::parse($jadwalHariIni->shift->jam_pulang);
                                    $durasi = $jamMasuk->diffInHours($jamPulang);
                                @endphp
                                <div class="text-sm md:text-base font-bold">{{ $durasi }} Jam</div>
                            </div>
                            
                            {{-- Jam Masuk --}}
                            <div class="p-3 md:p-4 bg-success/10 rounded-lg border border-success/20">
                                <div class="text-xs text-success/70 mb-1 flex items-center gap-1">
                                    <x-heroicon-o-arrow-right-on-rectangle class="w-3 h-3" />
                                    Jam Masuk
                                </div>
                                <div class="text-sm md:text-lg font-bold text-success">
                                    {{ Carbon\Carbon::parse($jadwalHariIni->shift->jam_masuk)->format('H:i') }} WIB
                                </div>
                            </div>
                            
                            {{-- Jam Pulang --}}
                            <div class="p-3 md:p-4 bg-error/10 rounded-lg border border-error/20">
                                <div class="text-xs text-error/70 mb-1 flex items-center gap-1">
                                    <x-heroicon-o-arrow-left-on-rectangle class="w-3 h-3" />
                                    Jam Pulang
                                </div>
                                <div class="text-sm md:text-lg font-bold text-error">
                                    {{ Carbon\Carbon::parse($jadwalHariIni->shift->jam_pulang)->format('H:i') }} WIB
                                </div>
                            </div>
                        </div>
                        
                        {{-- Lokasi --}}
                        @if($jadwalHariIni->lokasi)
                            <div class="flex items-center gap-2 md:gap-3 p-3 md:p-4 bg-info/10 rounded-lg border border-info/20">
                                <x-heroicon-o-map-pin class="w-5 h-5 md:w-6 md:h-6 text-info flex-shrink-0" />
                                <div class="flex-1">
                                    <div class="text-xs text-info/70">Lokasi Kerja</div>
                                    <div class="text-sm md:text-base font-bold text-info">{{ $jadwalHariIni->lokasi->nama_lokasi }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif($cutiHariIni)
                    <div class="text-center py-6 md:py-8">
                        <x-heroicon-o-calendar-days class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 text-warning opacity-50" />
                        <p class="text-sm md:text-base font-semibold text-warning">Hari ini Anda sedang Cuti</p>
                        <p class="text-xs md:text-sm text-base-content/60 mt-1">{{ $cutiHariIni->alasan }}</p>
                    </div>
                @elseif($izinHariIni)
                    <div class="text-center py-6 md:py-8">
                        <x-heroicon-o-exclamation-circle class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 text-error opacity-50" />
                        <p class="text-sm md:text-base font-semibold text-error">Hari ini Anda sedang Izin</p>
                        <p class="text-xs md:text-sm text-base-content/60 mt-1">{{ $izinHariIni->alasan }}</p>
                    </div>
                @elseif(Carbon\Carbon::today()->isWeekend())
                    <div class="text-center py-6 md:py-8">
                        <x-heroicon-o-calendar class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 text-error opacity-50" />
                        <p class="text-sm md:text-base font-semibold text-error">Hari Libur (Weekend)</p>
                        <p class="text-xs md:text-sm text-base-content/60 mt-1">Tidak ada jadwal kerja</p>
                    </div>
                @else
                    <div class="text-center py-6 md:py-8">
                        <x-heroicon-o-calendar-days class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 opacity-30" />
                        <p class="text-sm md:text-base font-semibold text-base-content/60">Tidak ada jadwal untuk hari ini</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- SECTION 2 — Informasi Shift Aktif --}}
        @if($jadwalHariIni && $jadwalHariIni->shift)
            <div class="card bg-base-100 border border-base-300">
                <div class="card-body p-4 md:p-6">
                    <h2 class="card-title text-base md:text-lg mb-3 md:mb-4">
                        <x-heroicon-o-information-circle class="w-4 h-4 md:w-5 md:h-5" />
                        Informasi Shift Aktif
                    </h2>
                    
                    <div class="space-y-2 md:space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-base-200">
                            <span class="text-xs md:text-sm text-base-content/70">Nama Shift</span>
                            <span class="text-xs md:text-sm font-bold">{{ $jadwalHariIni->shift->nama_shift }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-base-200">
                            <span class="text-xs md:text-sm text-base-content/70">Jam Masuk</span>
                            <span class="text-xs md:text-sm font-bold text-success">
                                {{ Carbon\Carbon::parse($jadwalHariIni->shift->jam_masuk)->format('H:i') }} WIB
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-base-200">
                            <span class="text-xs md:text-sm text-base-content/70">Jam Pulang</span>
                            <span class="text-xs md:text-sm font-bold text-error">
                                {{ Carbon\Carbon::parse($jadwalHariIni->shift->jam_pulang)->format('H:i') }} WIB
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-base-200">
                            <span class="text-xs md:text-sm text-base-content/70">Toleransi Terlambat</span>
                            <span class="text-xs md:text-sm font-bold text-warning">
                                {{ $jadwalHariIni->shift->toleransi_menit ?? 0 }} Menit
                            </span>
                        </div>
                        
                        @if($jadwalHariIni->lokasi)
                            <div class="flex justify-between items-center py-2 border-b border-base-200">
                                <span class="text-xs md:text-sm text-base-content/70">Lokasi Default</span>
                                <span class="text-xs md:text-sm font-bold">{{ $jadwalHariIni->lokasi->nama_lokasi }}</span>
                            </div>
                        @endif
                        
                        <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-base-200">
                            <div class="alert alert-info py-2 md:py-3">
                                <x-heroicon-o-information-circle class="w-4 h-4 md:w-5 md:h-5" />
                                <div class="text-xs md:text-sm">
                                    <p class="font-semibold mb-1">Aturan Absensi:</p>
                                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                                        <li>Absen harus dilakukan di lokasi yang sudah ditentukan</li>
                                        <li>Pastikan GPS dan kamera diaktifkan</li>
                                        <li>Absen maksimal {{ $jadwalHariIni->shift->toleransi_menit ?? 0 }} menit setelah jam masuk</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- SECTION 3 — Kalender Jadwal --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-3 md:p-6">
                {{-- Calendar Header --}}
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <h2 class="card-title text-base md:text-lg">
                        <x-heroicon-o-calendar class="w-4 h-4 md:w-5 md:h-5" />
                        <span class="hidden sm:inline">Kalender Jadwal</span>
                        <span class="sm:hidden">Kalender</span>
                    </h2>
                    
                    {{-- Month Navigation --}}
                    <div class="flex items-center gap-1 md:gap-2">
                        <button wire:click="previousMonth" class="btn btn-circle btn-xs md:btn-sm">
                            <x-heroicon-o-chevron-left class="w-3 h-3 md:w-4 md:h-4" />
                        </button>
                        <span class="text-xs md:text-base font-bold min-w-[100px] md:min-w-[150px] text-center">
                            {{ Carbon\Carbon::create($selectedYear, $selectedMonth)->locale('id')->isoFormat('MMM YYYY') }}
                        </span>
                        <button wire:click="nextMonth" class="btn btn-circle btn-xs md:btn-sm">
                            <x-heroicon-o-chevron-right class="w-3 h-3 md:w-4 md:h-4" />
                        </button>
                        <button wire:click="today" class="btn btn-xs md:btn-sm btn-ghost ml-1 md:ml-2">
                            <span class="hidden md:inline">Hari Ini</span>
                            <span class="md:hidden text-[10px]">Hari Ini</span>
                        </button>
                    </div>
                </div>

                {{-- Calendar Grid --}}
                <div class="grid grid-cols-7 gap-1 md:gap-2">
                    {{-- Days Header --}}
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                        <div class="text-center font-bold py-1 md:py-2 bg-primary/10 rounded text-primary text-[10px] md:text-sm">
                            <span class="hidden md:inline">{{ $day }}</span>
                            <span class="md:hidden">{{ substr($day, 0, 1) }}</span>
                        </div>
                    @endforeach

                    {{-- Calendar Days --}}
                    @php
                        $currentDate = $startOfCalendar->copy();
                    @endphp

                    @while ($currentDate <= $endOfCalendar)
                        @php
                            $isCurrentMonth = $currentDate->month == $selectedMonth;
                            $isToday = $currentDate->isToday();
                            $isWeekend = $currentDate->isWeekend();
                            $dateString = $currentDate->format('Y-m-d');
                            
                            // Check if this date is a national holiday
                            $holidayInfo = $hariLiburNasional[$dateString] ?? null;
                            $isNationalHoliday = $holidayInfo !== null;

                            $dayJadwal = $jadwals->first(function ($jadwal) use ($dateString) {
                                return $jadwal->tanggal->format('Y-m-d') === $dateString;
                            });
                            
                            $hasCuti = $cutisInMonth->first(function($cuti) use ($currentDate) {
                                return $currentDate->between($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                            });
                            
                            $hasIzin = $izinsInMonth->first(function($izin) use ($currentDate) {
                                return $currentDate->between($izin->tanggal_mulai, $izin->tanggal_selesai);
                            });
                        @endphp

                        <div class="min-h-[50px] md:min-h-[80px] p-1 md:p-2 border rounded transition-all
                            {{ $isCurrentMonth ? 'bg-base-100' : 'bg-base-200/30 opacity-50' }}
                            {{ $isToday ? 'ring-2 ring-primary border-primary shadow-md' : 'border-base-200' }}
                            {{ $isNationalHoliday && $isCurrentMonth ? 'bg-warning/5 border-warning/30' : '' }}
                            {{ $isWeekend && !$isNationalHoliday && $isCurrentMonth ? 'bg-error/5 border-error/30' : '' }}"
                            title="@if($isNationalHoliday){{ $holidayInfo['name'] }}@elseif($dayJadwal){{ $dayJadwal->shift->nama_shift }} - {{ Carbon\Carbon::parse($dayJadwal->shift->jam_masuk)->format('H:i') }}-{{ Carbon\Carbon::parse($dayJadwal->shift->jam_pulang)->format('H:i') }}@endif">
                            
                            {{-- Date Number --}}
                            <div class="text-[10px] md:text-sm font-bold mb-1
                                {{ $isToday ? 'text-primary' : ($isNationalHoliday ? 'text-warning' : ($isWeekend ? 'text-error' : 'text-base-content')) }}">
                                {{ $currentDate->format('d') }}
                                @if($isToday)
                                    <div class="inline-grid *:[grid-area:1/1]">
                                        <div class="status status-primary animate-ping"></div>
                                        <div class="status status-primary"></div>
                                    </div>
                                @elseif($isNationalHoliday)
                                    <div class="inline-grid *:[grid-area:1/1]">
                                        <div class="status status-warning animate-ping"></div>
                                        <div class="status status-warning"></div>
                                    </div>
                                @endif
                            </div>

                            {{-- Status Badges --}}
                            @if($isCurrentMonth)
                                @if($isNationalHoliday)
                                    <div class="hidden md:block">
                                        <div class="text-[8px] font-bold text-warning truncate">{{ Str::limit($holidayInfo['name'], 15) }}</div>
                                        <div class="text-[7px] text-warning/70">Libur Nasional</div>
                                    </div>
                                    <div class="md:hidden">
                                        <div class="badge badge-warning badge-xs w-full text-[7px] py-0 h-auto">Libur</div>
                                    </div>
                                @elseif($hasCuti)
                                    <div class="badge badge-warning badge-xs w-full text-[8px] md:text-[10px] py-0 md:py-1 h-auto">
                                        Cuti
                                    </div>
                                @elseif($hasIzin)
                                    <div class="badge badge-error badge-xs w-full text-[8px] md:text-[10px] py-0 md:py-1 h-auto">
                                        Izin
                                    </div>
                                @elseif($dayJadwal)
                                    <div class="hidden md:block">
                                        <div class="text-[9px] font-semibold text-info truncate">{{ $dayJadwal->shift->nama_shift }}</div>
                                        <div class="text-[8px] text-base-content/60">
                                            {{ Carbon\Carbon::parse($dayJadwal->shift->jam_masuk)->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="md:hidden">
                                        <div class="w-2 h-2 bg-info rounded-full mx-auto"></div>
                                    </div>
                                @elseif($isWeekend)
                                    <div class="badge badge-error badge-xs w-full text-[8px] md:text-[10px] py-0 md:py-1 h-auto">
                                        <span class="hidden md:inline">Libur</span>
                                        <span class="md:hidden">L</span>
                                    </div>
                                @endif
                            @endif
                        </div>

                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                </div>

                {{-- Legend --}}
                <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-base-200">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-[10px] md:text-xs">
                        <div class="flex items-center gap-1 md:gap-2">
                            <div class="w-3 h-3 bg-info rounded"></div>
                            <span>Kerja</span>
                        </div>
                        <div class="flex items-center gap-1 md:gap-2">
                            <div class="w-3 h-3 bg-error/20 border border-error rounded"></div>
                            <span>Libur Weekend</span>
                        </div>
                        <div class="flex items-center gap-1 md:gap-2">
                            <div class="w-3 h-3 bg-warning/20 border border-warning rounded"></div>
                            <span>Libur Nasional</span>
                        </div>
                        <div class="flex items-center gap-1 md:gap-2">
                            <div class="w-3 h-3 bg-warning border border-warning rounded"></div>
                            <span>Cuti</span>
                        </div>
                        <div class="flex items-center gap-1 md:gap-2">
                            <div class="w-3 h-3 bg-error border border-error rounded"></div>
                            <span>Izin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4 — Ringkasan Bulanan --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4 md:p-6">
                <h2 class="card-title text-base md:text-lg mb-3 md:mb-4">
                    <x-heroicon-o-chart-bar class="w-4 h-4 md:w-5 md:h-5" />
                    Ringkasan {{ Carbon\Carbon::create($selectedYear, $selectedMonth)->locale('id')->isoFormat('MMMM YYYY') }}
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4">
                    {{-- Total Hari Kerja --}}
                    <div class="stat bg-info/10 rounded-lg p-3 md:p-4 border border-info/20">
                        <div class="stat-figure text-info">
                            <x-heroicon-o-briefcase class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                        <div class="stat-title text-[10px] md:text-xs">Hari Kerja</div>
                        <div class="stat-value text-lg md:text-3xl text-info">{{ $totalHariKerja }}</div>
                        <div class="stat-desc text-[9px] md:text-xs">Hari</div>
                    </div>
                    
                    {{-- Total Libur --}}
                    <div class="stat bg-error/10 rounded-lg p-3 md:p-4 border border-error/20">
                        <div class="stat-figure text-error">
                            <x-heroicon-o-calendar class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                        <div class="stat-title text-[10px] md:text-xs">Libur</div>
                        <div class="stat-value text-lg md:text-3xl text-error">{{ $totalLibur }}</div>
                        <div class="stat-desc text-[9px] md:text-xs">Hari</div>
                    </div>
                    
                    {{-- Total Cuti --}}
                    <div class="stat bg-warning/10 rounded-lg p-3 md:p-4 border border-warning/20">
                        <div class="stat-figure text-warning">
                            <x-heroicon-o-calendar-days class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                        <div class="stat-title text-[10px] md:text-xs">Cuti</div>
                        <div class="stat-value text-lg md:text-3xl text-warning">{{ $totalCuti }}</div>
                        <div class="stat-desc text-[9px] md:text-xs">Hari</div>
                    </div>
                    
                    {{-- Total Izin --}}
                    <div class="stat bg-secondary/10 rounded-lg p-3 md:p-4 border border-secondary/20">
                        <div class="stat-figure text-secondary">
                            <x-heroicon-o-exclamation-circle class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                        <div class="stat-title text-[10px] md:text-xs">Izin</div>
                        <div class="stat-value text-lg md:text-3xl text-secondary">{{ $totalIzin }}</div>
                        <div class="stat-desc text-[9px] md:text-xs">Hari</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 5 — Riwayat Jadwal --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body p-4 md:p-6">
                <h2 class="card-title text-base md:text-lg mb-3 md:mb-4">
                    <x-heroicon-o-clock class="w-4 h-4 md:w-5 md:h-5" />
                    Riwayat Jadwal (30 Hari Terakhir)
                </h2>
                
                @if($riwayatJadwal->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-xs md:table-sm">
                            <thead>
                                <tr>
                                    <th class="text-[10px] md:text-xs">Tanggal</th>
                                    <th class="text-[10px] md:text-xs">Shift</th>
                                    <th class="text-[10px] md:text-xs hidden md:table-cell">Jam Kerja</th>
                                    <th class="text-[10px] md:text-xs hidden sm:table-cell">Lokasi</th>
                                    <th class="text-[10px] md:text-xs">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatJadwal as $riwayat)
                                    <tr class="hover">
                                        <td class="text-[10px] md:text-xs">
                                            <div class="font-bold">{{ Carbon\Carbon::parse($riwayat->tanggal)->format('d/m/Y') }}</div>
                                            <div class="text-[9px] md:text-xs text-base-content/60">
                                                {{ Carbon\Carbon::parse($riwayat->tanggal)->isoFormat('dddd') }}
                                            </div>
                                        </td>
                                        <td class="text-[10px] md:text-xs font-semibold">{{ $riwayat->shift->nama_shift }}</td>
                                        <td class="text-[10px] md:text-xs hidden md:table-cell">
                                            {{ Carbon\Carbon::parse($riwayat->shift->jam_masuk)->format('H:i') }} - 
                                            {{ Carbon\Carbon::parse($riwayat->shift->jam_pulang)->format('H:i') }}
                                        </td>
                                        <td class="text-[10px] md:text-xs hidden sm:table-cell">
                                            {{ $riwayat->lokasi->nama_lokasi ?? '-' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-sm badge-soft md:badge-md badge-info text-[9px] md:text-xs">
                                                {{ ucfirst($riwayat->status ?? 'active') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 md:py-8">
                        <x-heroicon-o-clock class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 opacity-30" />
                        <p class="text-xs md:text-sm text-base-content/60">Belum ada riwayat jadwal</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
