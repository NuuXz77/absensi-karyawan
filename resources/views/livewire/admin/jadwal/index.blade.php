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

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button class="btn btn-success btn-sm gap-2" wire:click="$dispatch('open-auto-generate-modal')">
                        <x-heroicon-o-sparkles class="w-5 h-5" />
                        <span class="hidden sm:inline">Auto Generate Jadwal</span>
                    </button>
                    <button class="btn btn-primary btn-sm gap-2" wire:click="$dispatch('open-create-modal')">
                        <x-heroicon-o-plus class="w-5 h-5" />
                        <span class="hidden sm:inline">Tambah Jadwal</span>
                    </button>
                    <button class="btn btn-error btn-sm gap-2" wire:click="$dispatch('open-bulk-delete-modal')">
                        <x-heroicon-o-trash class="w-5 h-5" />
                        <span class="hidden sm:inline">Hapus Jadwal Massal</span>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-base-200">
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
    <div class="card bg-base-300 border border-base-100">
        <div class="card-body">
            @if(!$filterDepartemen)
                <div class="alert alert-info">
                    <x-heroicon-o-information-circle class="w-6 h-6" />
                    <span>Silakan pilih <strong>Departemen</strong> terlebih dahulu untuk menampilkan jadwal.</span>
                </div>
            @endif

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 {{ !$filterDepartemen ? 'opacity-30 pointer-events-none' : '' }}">
                <!-- Days Header -->
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab','Min'] as $day)
                    <div class="text-center font-bold py-3 bg-base-200 rounded-lg">
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
                        $dateString = $currentDate->format('Y-m-d');
                        $dayJadwals = $jadwals->filter(function($jadwal) use ($dateString) {
                            return $jadwal->tanggal->format('Y-m-d') === $dateString;
                        });
                    @endphp

                    <div class="min-h-[120px] p-2 border rounded-lg {{ $isCurrentMonth ? 'bg-base-100' : 'bg-base-200 opacity-50' }} {{ $isToday ? 'ring-2 ring-primary' : '' }}"
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
                        <div class="flex items-start justify-between mb-2 pb-1 border-b border-base-300">
                            <div class="text-sm font-semibold {{ $isToday ? 'text-primary' : '' }}">
                                {{ $currentDate->format('d') }}
                            </div>
                            @php
                                $totalKaryawan = $dayJadwals->unique('karyawan_id')->count();
                                $departemens = $dayJadwals->pluck('karyawan.departemen.nama_departemen')->unique()->filter();
                            @endphp
                            @if($totalKaryawan > 0)
                                <div class="flex flex-col gap-0.5 items-end">
                                    <div class="badge badge-xs badge-info gap-1" title="{{ $totalKaryawan }} karyawan bekerja">
                                        <x-heroicon-o-user-group class="w-2.5 h-2.5" />
                                        {{ $totalKaryawan }}
                                    </div>
                                    @if($departemens->count() > 0)
                                        <div class="flex flex-wrap gap-0.5 justify-end max-w-[80px]">
                                            @foreach($departemens->take(2) as $dept)
                                                <span class="text-[8px] px-1 py-0.5 bg-base-200 rounded truncate max-w-full" title="{{ $dept }}">
                                                    {{ Str::limit($dept, 10, '') }}
                                                </span>
                                            @endforeach
                                            @if($departemens->count() > 2)
                                                <span class="text-[8px] px-1 py-0.5 bg-base-200 rounded" title="{{ $departemens->skip(2)->implode(', ') }}">
                                                    +{{ $departemens->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Cuti & Izin Badges --}}
                        @php
                            $cutiForDate = $cutisInMonth->filter(function ($cuti) use ($currentDate) {
                                return $currentDate->between($cuti->tanggal_mulai, $cuti->tanggal_selesai);
                            });
                            $izinForDate = $izinsInMonth->filter(function ($izin) use ($currentDate) {
                                return $currentDate->isSameDay($izin->tanggal);
                            });
                        @endphp

                        @if ($cutiForDate->count() > 0)
                            @foreach ($cutiForDate as $cuti)
                                <div class="text-xs mb-1 p-1 bg-warning/20 rounded border-l-2 border-warning truncate"
                                    title="Cuti: {{ $cuti->karyawan->nama_lengkap }}">
                                    <x-heroicon-o-calendar-days class="w-3 h-3 inline" />
                                    <span class="text-[10px]">{{ $cuti->karyawan->nama_lengkap }} (Cuti)</span>
                                </div>
                            @endforeach
                        @endif

                        @if ($izinForDate->count() > 0)
                            @foreach ($izinForDate as $izin)
                                <div class="text-xs mb-1 p-1 bg-error/20 rounded border-l-2 border-error truncate"
                                    title="Izin: {{ $izin->karyawan->nama_lengkap }}">
                                    <x-heroicon-o-exclamation-circle class="w-3 h-3 inline" />
                                    <span class="text-[10px]">{{ $izin->karyawan->nama_lengkap }} (Izin)</span>
                                </div>
                            @endforeach
                        @endif

                        {{-- Jadwal Items - Grouped by Departemen then Shift --}}
                        @php
                            $groupedJadwals = $dayJadwals->groupBy(function($jadwal) {
                                return $jadwal->karyawan->departemen->nama_departemen ?? 'Tanpa Departemen';
                            });
                        @endphp
                        
                        @foreach ($groupedJadwals as $departemen => $jadwalsInDept)
                            @php
                                $totalKaryawanInDept = $jadwalsInDept->count();
                                $shiftGroups = $jadwalsInDept->groupBy(function($jadwal) {
                                    return $jadwal->shift->nama_shift ?? 'Tanpa Shift';
                                });
                            @endphp
                            <div class="text-xs mb-2 p-2 bg-base-200 rounded-lg">
                                {{-- Departemen Header --}}
                                {{-- <div class="font-bold text-sm mb-1 flex items-center gap-1">
                                    <span class="truncate">{{ $departemen }}</span>
                                    <span class="badge badge-xs badge-primary">{{ $totalKaryawanInDept }}</span>
                                </div> --}}
                                
                                {{-- Shift Breakdown --}}
                                @foreach ($shiftGroups as $shiftName => $jadwalsInShift)
                                    @php
                                        $shiftCount = $jadwalsInShift->count();
                                        $karyawanNames = $jadwalsInShift->pluck('karyawan.nama_lengkap')->implode(', ');
                                    @endphp
                                    <a href="{{ route('admin.jadwal.detail') }}?tanggal={{ $dateString }}&shift_id={{ $jadwalsInShift->first()->shift_id }}&departemen_id={{ $jadwalsInShift->first()->karyawan->departemen_id }}"
                                        wire:navigate
                                        class="text-xs py-1.5 px-2 bg-info/20 rounded border-l-2 border-info mb-1 hover:bg-info/40 hover:scale-[1.02] transition-all group relative block"
                                        draggable="true"
                                        x-data="{ jadwalIds: {{ $jadwalsInShift->pluck('id')->toJson() }} }"
                                        @dragstart.stop="$event.dataTransfer.setData('text/plain', JSON.stringify({ jadwalIds: jadwalIds, shiftId: {{ $jadwalsInShift->first()->shift_id }} }))"
                                        title="Klik untuk melihat detail - Karyawan: {{ $karyawanNames }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5 flex-1">
                                                <span class="text-[11px] font-medium">{{ $shiftName }}</span>
                                                <span class="text-[10px] opacity-70">👥 {{ $shiftCount }}</span>
                                            </div>
                                            <x-heroicon-o-arrow-right class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                        </div>
                                    </a>
                                @endforeach
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
                        <div class="w-4 h-4 rounded bg-error/20 border-l-2 border-error"></div>
                        <span>Libur</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded ring-2 ring-primary"></div>
                        <span>Hari Ini</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-cursor-arrow-ripple class="w-4 h-4" />
                        <span>Drag & Drop untuk memindah jadwal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Modal Components --}}
    <livewire:admin.jadwal.modals.bulk-delete />
    <livewire:admin.jadwal.modals.auto-generate />
    <livewire:admin.jadwal.modals.create />
    <livewire:admin.jadwal.modals.edit />
    <livewire:admin.jadwal.modals.delete />
</div>
