<div>
    <!-- Header Controls -->
    <div class="card bg-base-100 border border-base-300 mb-4">
        <div class="card-body">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <!-- Month Navigation -->
                <div class="flex items-center gap-2">
                    <button wire:click="previousMonth" class="btn btn-circle btn-sm">
                        <x-heroicon-o-chevron-left class="w-5 h-5" />
                    </button>
                    <h2 class="text-xl font-bold min-w-[200px] text-center">
                        {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->locale('id')->isoFormat('MMMM YYYY') }}
                    </h2>
                    <button wire:click="nextMonth" class="btn btn-circle btn-sm">
                        <x-heroicon-o-chevron-right class="w-5 h-5" />
                    </button>
                    <button wire:click="today" class="btn btn-sm btn-ghost">Hari Ini</button>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Action Buttons -->
                    <livewire:admin.jadwal.modals.auto-generate />
                    <livewire:admin.jadwal.modals.create />
                    <livewire:admin.jadwal.modals.bulk-delete />
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-base-200">
                <div class="form-control">
                    <label class="label py-0 pb-1">
                        <span class="label-text text-xs">Lokasi <span class="text-error">*</span></span>
                    </label>
                    <select wire:model.live="filterLokasi" class="select select-bordered select-sm" required>
                        <option value="" disabled selected>Pilih Lokasi</option>
                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}">{{ $lokasi->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="label py-0 pb-1">
                        <span class="label-text text-xs">Departemen <span class="text-error">*</span></span>
                    </label>
                    <select wire:model.live="filterDepartemen" class="select select-bordered select-sm" required>
                        <option value="" disabled selected>Pilih Departemen</option>
                        @foreach ($departemens as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="label py-0 pb-1">
                        <span class="label-text text-xs">Shift (Opsional)</span>
                    </label>
                    <select wire:model.live="filterShift" class="select select-bordered select-sm">
                        <option value="">Semua Shift</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->nama_shift }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="resetFilters" class="btn btn-sm btn-ghost self-end">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            @if (!$filterLokasi || !$filterDepartemen)
                <div class="alert alert-info">
                    <x-heroicon-o-information-circle class="w-6 h-6" />
                    <span>Silakan pilih <strong>Lokasi</strong> dan <strong>Departemen</strong> terlebih dahulu untuk
                        menampilkan jadwal.</span>
                </div>
            @endif

            <!-- Calendar Grid -->
            <div
                class="grid grid-cols-7 gap-3 {{ !$filterLokasi || !$filterDepartemen ? 'opacity-30 pointer-events-none' : '' }}">
                <!-- Days Header -->
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <div
                        class="text-center font-bold py-3 bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl border border-primary/20 text-primary">
                        {{ $day }}
                    </div>
                @endforeach

                <!-- Calendar Days -->
                @php
                    $currentDate = $startOfCalendar->copy();
                @endphp

                @while ($currentDate <= $endOfCalendar)
                    @php
                        $isCurrentMonth = $currentDate->month == $selectedMonth;
                        $isToday = $currentDate->isToday();
                        $isWeekend = $currentDate->isWeekend(); // Sabtu atau Minggu
                        $dateString = $currentDate->format('Y-m-d');

                        // Check if this date is a national holiday
                        $holidayInfo = $hariLiburNasional[$dateString] ?? null;
                        $isNationalHoliday = $holidayInfo !== null;
                        $isHoliday = $isWeekend || $isNationalHoliday;

                        $dayJadwals = $jadwals->filter(function ($jadwal) use ($dateString) {
                            return $jadwal->tanggal->format('Y-m-d') === $dateString;
                        });
                    @endphp

                    <div class="min-h-[140px] p-3 border-2 rounded-xl transition-all duration-200 
                        {{ $isCurrentMonth ? 'bg-base-100 shadow-sm hover:shadow-md' : 'bg-base-200/30 opacity-60' }} 
                        {{ $isNationalHoliday && $isCurrentMonth ? 'border-warning/60 bg-warning/5 hover:border-warning' : ($isWeekend && $isCurrentMonth ? 'border-error/60 bg-error/5 hover:border-error' : ($isCurrentMonth ? 'border-base-200/50 hover:border-base-200' : 'border-base-200/30')) }}
                        {{ $isToday ? 'ring-2 ring-primary ring-offset-2 ring-offset-base-300 shadow-lg border-primary/50' : '' }}"
                        x-data="{
                            dragover: false,
                            date: '{{ $currentDate->format('Y-m-d') }}'
                        }" @dragover.prevent="dragover = true"
                        @dragleave.prevent="dragover = false"
                        @drop.prevent="
                            dragover = false;
                            let data = JSON.parse($event.dataTransfer.getData('text/plain'));
                            $wire.updateJadwal(data.jadwalId, date, data.shiftId);
                         "
                        :class="{ 'ring-2 ring-primary bg-primary/5': dragover }">
                        {{-- Header with Date and Info --}}
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-base-200">
                            <div class="flex items-center gap-2">
                                <div
                                    class="text-lg font-bold {{ $isToday ? 'text-primary' : ($isNationalHoliday ? 'text-warning' : ($isWeekend ? 'text-error' : 'text-base-content')) }}">
                                    {{ $currentDate->format('d') }}
                                </div>
                                @if ($isToday)
                                    <div class="inline-grid *:[grid-area:1/1]">
                                        <div class="status status-primary animate-ping"></div>
                                        <div class="status status-primary"></div>
                                    </div>
                                @endif
                                @if ($isNationalHoliday)
                                    <div class="inline-grid *:[grid-area:1/1]">
                                        <div class="status status-warning animate-ping"></div>
                                        <div class="status status-warning"></div>
                                    </div>
                                @elseif($isWeekend)
                                    <div class="inline-grid *:[grid-area:1/1]">
                                        <div class="status status-error animate-ping"></div>
                                        <div class="status status-error"></div>
                                    </div>
                                @endif
                            </div>
                            @php
                                $totalKaryawan = $dayJadwals->unique('karyawan_id')->count();
                                $departemens = $dayJadwals
                                    ->pluck('karyawan.departemen.nama_departemen')
                                    ->unique()
                                    ->filter();
                            @endphp
                            @if ($totalKaryawan > 0)
                                <div class="badge badge-sm gap-1.5 bg-info/10 text-info border-info/30"
                                    title="{{ $totalKaryawan }} karyawan bekerja">
                                    {{-- <x-heroicon-o-user-group class="w-3.5 h-3.5" /> --}}
                                    <span class="font-semibold">{{ $totalKaryawan }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- National Holiday Info Card --}}
                        @if ($isNationalHoliday)
                            <div
                                class="mb-2 p-2.5 bg-gradient-to-r from-warning/25 to-warning/15 rounded-lg border border-warning/40 shadow-sm">
                                <div class="flex items-center gap-2">
                                    {{-- <x-heroicon-o-flag class="w-4 h-4 text-warning flex-shrink-0" /> --}}
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-warning">{{ $holidayInfo['name'] }}</p>
                                        <p class="text-[10px] text-warning/70">Hari Libur Nasional</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Cuti & Izin Badges --}}
                        @php
                            $cutiForDate = $cutisInMonth->filter(function ($cuti) use ($currentDate) {
                                return $cuti->tanggal_mulai &&
                                    $cuti->tanggal_selesai &&
                                    $currentDate->between($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                            });
                            $izinForDate = $izinsInMonth->filter(function ($izin) use ($currentDate) {
                                return $izin->tanggal && $currentDate->isSameDay($izin->tanggal);
                            });
                        @endphp

                        @if ($cutiForDate->count() > 0)
                            @foreach ($cutiForDate->take(2) as $cuti)
                                <div class="flex items-center gap-1.5 text-xs mb-1.5 p-2 bg-gradient-to-r from-warning/20 to-warning/10 rounded-lg border border-warning/30 hover:shadow-sm transition-shadow"
                                    title="Cuti: {{ $cuti->karyawan->nama_lengkap }}">
                                    <x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-warning flex-shrink-0" />
                                    <span
                                        class="text-[11px] font-medium truncate text-warning-content">{{ Str::limit($cuti->karyawan->nama_lengkap, 20) }}</span>
                                    <span class="badge badge-warning badge-xs ml-auto">Cuti</span>
                                </div>
                            @endforeach
                            @if ($cutiForDate->count() > 2)
                                <div class="text-[10px] text-warning ml-2 font-medium">+{{ $cutiForDate->count() - 2 }}
                                    lainnya</div>
                            @endif
                        @endif

                        @if ($izinForDate->count() > 0)
                            @foreach ($izinForDate->take(2) as $izin)
                                <div class="flex items-center gap-1.5 text-xs mb-1.5 p-2 bg-gradient-to-r from-error/20 to-error/10 rounded-lg border border-error/30 hover:shadow-sm transition-shadow"
                                    title="Izin: {{ $izin->karyawan->nama_lengkap }}">
                                    <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 text-error flex-shrink-0" />
                                    <span
                                        class="text-[11px] font-medium truncate text-error-content">{{ Str::limit($izin->karyawan->nama_lengkap, 20) }}</span>
                                    <span class="badge badge-error badge-xs ml-auto">Izin</span>
                                </div>
                            @endforeach
                            @if ($izinForDate->count() > 2)
                                <div class="text-[10px] text-error ml-2 font-medium">+{{ $izinForDate->count() - 2 }}
                                    lainnya</div>
                            @endif
                        @endif

                        {{-- Jadwal Items - Grouped by Departemen then Shift --}}
                        @php
                            $groupedJadwals = $dayJadwals->groupBy(function ($jadwal) {
                                return $jadwal->karyawan->departemen_id ?? 0;
                            });
                        @endphp

                        @foreach ($groupedJadwals as $departemenId => $jadwalsInDept)
                            @php
                                $departemenName =
                                    $jadwalsInDept->first()->karyawan->departemen->nama_departemen ??
                                    'Tanpa Departemen';
                                $shiftGroups = $jadwalsInDept->groupBy(function ($jadwal) {
                                    return $jadwal->shift_id;
                                });
                            @endphp
                            <a href="{{ route('admin.jadwal.detail') }}?tanggal={{ $dateString }}&departemen_id={{ $departemenId }}"
                                wire:navigate
                                class="group relative block p-3 bg-gradient-to-br from-base-200/80 to-base-200/40 hover:from-info/20 hover:to-info/10 rounded-xl border border-base-300 hover:border-info/50 hover:shadow-lg transition-all duration-300 overflow-hidden"
                                title="Klik untuk melihat detail jadwal {{ $departemenName }}">

                                {{-- Data Content (hidden on hover) --}}
                                <div class="transition-all duration-300 group-hover:opacity-0 group-hover:scale-95">
                                    {{-- Departemen Header --}}
                                    <div
                                        class="text-center mb-2 pb-2 border-b border-base-300 group-hover:border-info/30 transition-colors">
                                        <h4
                                            class="text-xs font-bold text-base-content/80 group-hover:text-info transition-colors">
                                            {{ $departemenName }}
                                        </h4>
                                    </div>

                                    {{-- Shift List --}}
                                    <div class="space-y-1.5">
                                        @foreach ($shiftGroups as $shiftId => $jadwalsInShift)
                                            @php
                                                $shift = $jadwalsInShift->first()->shift;
                                                $shiftCount = $jadwalsInShift->count();
                                            @endphp
                                            <div class="flex items-center justify-between text-xs py-1">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-medium text-base-content/70">
                                                        {{ $shift->nama_shift ?? 'N/A' }}
                                                    </span>
                                                </div>
                                                <div class="badge badge-xs gap-1 bg-info/15 text-info border-info/30">
                                                    {{ $shiftCount }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Arrow Icon (centered, shown on hover) --}}
                                <div
                                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none">
                                    <div class="flex flex-col items-center gap-2">
                                        <x-heroicon-o-arrow-right
                                            class="w-8 h-8 text-info transform scale-0 group-hover:scale-100 transition-transform duration-300" />
                                        <span class="text-xs font-semibold text-info">Lihat Detail</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @php
                        $currentDate->addDay();
                    @endphp
                @endwhile
            </div>

            <!-- Legend -->
            <div class="mt-6 pt-4 border-t border-base-200">
                <h3 class="text-sm font-semibold mb-3 text-base-content/70">Keterangan:</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                    <div class="flex items-center gap-2 p-2 bg-base-200/50 rounded-lg">
                        <div
                            class="w-5 h-5 rounded-lg bg-gradient-to-br from-info/15 to-info/5 border border-info/30 flex-shrink-0">
                        </div>
                        <span class="text-xs">Jadwal Kerja</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 bg-base-200/50 rounded-lg">
                        <div class="inline-grid *:[grid-area:1/1]">
                            <div class="status status-error animate-ping"></div>
                            <div class="status status-error"></div>
                        </div> <span class="text-xs">Hari Libur (Weekend)</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 bg-base-200/50 rounded-lg">
                        <div class="inline-grid *:[grid-area:1/1]">
                            <div class="status status-warning animate-ping"></div>
                            <div class="status status-warning"></div>
                        </div>
                        <span class="text-xs">Libur Nasional</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 bg-base-200/50 rounded-lg">
                        <div class="inline-grid *:[grid-area:1/1]">
                            <div class="status status-primary animate-ping"></div>
                            <div class="status status-primary"></div>
                        </div>
                        <span class="text-xs">Hari Ini</span>
                    </div>
                </div>
                <div
                    class="mt-3 flex items-center gap-2 text-xs text-base-content/60 bg-info/5 p-2 rounded-lg border border-info/20">
                    <x-heroicon-o-cursor-arrow-ripple class="w-4 h-4 text-info" />
                    <span>Tip: Drag & drop kartu jadwal untuk memindahkan ke tanggal lain</span>
                </div>
            </div>
        </div>
    </div>
</div>
