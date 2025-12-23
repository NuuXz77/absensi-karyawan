<div>
    <!-- Header Controls -->
    <div class="card bg-base-300 border border-base-100 mb-4">
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

                <!-- Filters -->
                <div class="flex gap-2">
                    <select wire:model.live="filterKaryawan" class="select select-bordered select-sm">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterShift" class="select select-bordered select-sm">
                        <option value="">Semua Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->nama_shift }}</option>
                        @endforeach
                    </select>
                    <button wire:click="resetFilters" class="btn btn-sm btn-ghost">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card bg-base-300 border border-base-100">
        <div class="card-body">
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1">
                <!-- Days Header -->
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                    <div class="text-center font-bold py-3 bg-base-200 rounded-lg">
                        {{ $day }}
                    </div>
                @endforeach

                <!-- Calendar Days -->
                @php
                    $currentDate = $startOfCalendar->copy();
                @endphp
                
                @while($currentDate <= $endOfCalendar)
                    @php
                        $isCurrentMonth = $currentDate->month == $selectedMonth;
                        $isToday = $currentDate->isToday();
                        $dayJadwals = $jadwals->where('tanggal', $currentDate->format('Y-m-d'));
                    @endphp
                    
                    <div class="min-h-[120px] p-2 border rounded-lg {{ $isCurrentMonth ? 'bg-base-100' : 'bg-base-200 opacity-50' }} {{ $isToday ? 'ring-2 ring-primary' : '' }}">
                        <div class="text-sm font-semibold mb-1 {{ $isToday ? 'text-primary' : '' }}">
                            {{ $currentDate->format('d') }}
                        </div>
                        
                        @foreach($dayJadwals as $jadwal)
                            <div class="text-xs mb-1 p-1 bg-info/20 rounded border-l-2 border-info truncate" title="{{ $jadwal->karyawan->nama_lengkap }} - {{ $jadwal->shift->nama_shift }}">
                                <div class="font-semibold">{{ $jadwal->shift->nama_shift }}</div>
                                <div class="text-[10px] opacity-70">{{ $jadwal->karyawan->nama_lengkap }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    @php
                        $currentDate->addDay();
                    @endphp
                @endwhile
            </div>

            <!-- Legend -->
            <div class="mt-4 pt-4 border-t border-base-200">
                <div class="flex flex-wrap gap-4 items-center text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-info/20 border-l-2 border-info"></div>
                        <span>Jadwal Kerja</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded ring-2 ring-primary"></div>
                        <span>Hari Ini</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
