<div class="space-y-6">
    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-check-circle class="w-6 h-6 text-success" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Hadir</p>
                        <p class="text-lg font-bold text-success">{{ $totalHadir }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-clock class="w-6 h-6 text-warning" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Terlambat</p>
                        <p class="text-lg font-bold text-warning">{{ $totalTerlambat }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-document-text class="w-6 h-6 text-info" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Izin</p>
                        <p class="text-lg font-bold text-info">{{ $totalIzin }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-calendar class="w-6 h-6 text-secondary" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Cuti</p>
                        <p class="text-lg font-bold text-secondary">{{ $totalCuti }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm col-span-2 lg:col-span-1 sm:col-span-2">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-x-circle class="w-6 h-6 text-error" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Alpha</p>
                        <p class="text-lg font-bold text-error">{{ $totalAlpha }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            {{-- Top Section: Filters & Actions --}}
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                {{-- Left: Search & Filter --}}
                <div class="flex gap-2 w-full">
                    {{-- Search Input --}}
                    <label class="input input-sm">
                        <x-bi-search class="w-3" />
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tanggal..." />
                    </label>

                    {{-- Filter Dropdown --}}
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterBulan || $filterStatus)
                                <span
                                    class="badge badge-primary badge-sm">{{ ($filterBulan ? 1 : 0) + ($filterStatus ? 1 : 0) }}</span>
                            @endif
                        </label>
                        <div tabindex="0"
                            class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                {{-- Filter Bulan --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Bulan</span></label>
                                    <select wire:model.live="filterBulan" class="select select-bordered select-sm">
                                        <option value="">Semua Bulan</option>
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>

                                {{-- Filter Tahun --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Tahun</span></label>
                                    <select wire:model.live="filterTahun" class="select select-bordered select-sm">
                                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Filter Status --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Status</span></label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="tepat_waktu">Tepat Waktu</option>
                                        <option value="terlambat">Terlambat</option>
                                        <option value="izin">Izin</option>
                                        <option value="cuti">Cuti</option>
                                        <option value="alpha">Alpha</option>
                                    </select>
                                </div>

                                <button wire:click="resetFilters" class="btn btn-ghost btn-sm w-full">Reset
                                    Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            @php
                $columns = [
                    ['label' => 'Tanggal', 'class' => 'w-40'],
                    ['label' => 'Jam Masuk'],
                    ['label' => 'Jam Pulang'],
                    ['label' => 'Lokasi'],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center w-20'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$absensi" :sortField="null" :sortDirection="null"
                emptyMessage="Tidak ada data absensi" emptyIcon="heroicon-o-calendar">
                @foreach ($absensi as $index => $item)
                    <tr wire:key="absensi-{{ $item->id }}" class="hover:bg-base-200 transition-colors duration-150">
                        <td>
                            <div class="flex items-center gap-3">
                                <x-heroicon-o-calendar class="w-5 h-5 text-base-content/60" />
                                <div>
                                    <div class="font-bold">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </div>
                                    <div class="text-sm opacity-50">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($item->jam_masuk)
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 text-success" />
                                    <span>{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</span>
                                </div>
                            @else
                                <span class="text-base-content/40">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($item->jam_pulang)
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4 text-error" />
                                    <span>{{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}</span>
                                </div>
                            @else
                                <span class="text-base-content/40">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-map-pin class="w-4 h-4 text-primary" />
                                <span>{{ $item->lokasi->nama_lokasi }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match ($item->status) {
                                    'tepat_waktu' => 'badge-success',
                                    'terlambat' => 'badge-warning',
                                    'izin' => 'badge-info',
                                    'cuti' => 'badge-secondary',
                                    'alpha' => 'badge-error',
                                    default => 'badge-ghost',
                                };
                                $statusText = match ($item->status) {
                                    'tepat_waktu' => 'Tepat Waktu',
                                    'terlambat' => 'Terlambat',
                                    'izin' => 'Izin',
                                    'cuti' => 'Cuti',
                                    'alpha' => 'Alpha',
                                    default => ucfirst($item->status),
                                };
                            @endphp
                            <span class="badge badge-soft badge-sm {{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('karyawan.kehadiran.riwayat-absensi.detail', $item->id) }}"
                                wire:navigate class="btn btn-sm btn-ghost btn-circle">
                                <x-heroicon-o-eye class="w-5 h-5" />
                            </a>
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            {{-- Footer: Pagination --}}
            <div class="mt-6 pt-4 border-t border-base-300">
                <div class="flex flex-col gap-4">
                    {{-- Data Info --}}
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Menampilkan <span class="font-semibold">{{ $absensi->firstItem() ?? 0 }}</span>
                        sampai <span class="font-semibold">{{ $absensi->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold">{{ $absensi->total() }}</span> data
                    </div>

                    {{-- Pagination Component --}}
                    <x-partials.pagination :paginator="$absensi" :perPage="$perPage ?? 10" />
                </div>
            </div>
        </div>
    </div>
</div>
