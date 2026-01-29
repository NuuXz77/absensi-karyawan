<div class="space-y-6">
    {{-- Statistik Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-document-text class="w-6 h-6 text-primary" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Saldo Izin</p>
                        <p class="text-lg font-bold text-primary">{{ $saldoIzin ? $saldoIzin->sisa_izin : 0 }} / {{ $saldoIzin ? $saldoIzin->total_izin : 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-info" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Total Izin</p>
                        <p class="text-lg font-bold text-info">{{ $totalIzin }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-check-circle class="w-6 h-6 text-success" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Diterima</p>
                        <p class="text-lg font-bold text-success">{{ $totalDiterima }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center gap-3">
                    <div>
                        <x-heroicon-o-x-circle class="w-6 h-6 text-error" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/60">Ditolak</p>
                        <p class="text-lg font-bold text-error">{{ $totalDitolak }}</p>
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
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari tanggal, keterangan..." />
                    </label>

                    {{-- Filter Dropdown --}}
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterBulan || $filterStatus || $filterJenisIzin)
                                <span
                                    class="badge badge-primary badge-sm">{{ ($filterBulan ? 1 : 0) + ($filterStatus ? 1 : 0) + ($filterJenisIzin ? 1 : 0) }}</span>
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

                                {{-- Filter Jenis Izin --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Jenis Izin</span></label>
                                    <select wire:model.live="filterJenisIzin" class="select select-bordered select-sm">
                                        <option value="">Semua Jenis</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="keperluan_pribadi">Keperluan Pribadi</option>
                                        <option value="dinas">Dinas</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>

                                {{-- Filter Status --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Status</span></label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
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
                    ['label' => 'Tanggal Mulai', 'class' => 'w-32'],
                    ['label' => 'Tanggal Selesai', 'class' => 'w-32'],
                    ['label' => 'Jenis Izin'],
                    ['label' => 'Keterangan'],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center w-20'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$izin" :sortField="null" :sortDirection="null"
                emptyMessage="Tidak ada data izin" emptyIcon="heroicon-o-document-text">
                @foreach ($izin as $index => $item)
                    <tr wire:key="izin-{{ $item->id }}" class="hover:bg-base-200 transition-colors duration-150">
                        {{-- Tanggal Mulai --}}
                        <td class="whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 text-base-content/60" />
                                <span class="text-sm">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</span>
                            </div>
                        </td>

                        {{-- Tanggal Selesai --}}
                        <td class="whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 text-base-content/60" />
                                <span class="text-sm">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</span>
                            </div>
                        </td>

                        {{-- Jenis Izin --}}
                        <td>
                            <span class="badge badge-sm badge-outline">
                                @if ($item->jenis_izin == 'sakit')
                                    Sakit
                                @elseif ($item->jenis_izin == 'keperluan_pribadi')
                                    Keperluan Pribadi
                                @elseif ($item->jenis_izin == 'dinas')
                                    Dinas
                                @else
                                    Lainnya
                                @endif
                            </span>
                        </td>

                        {{-- Keterangan --}}
                        <td>
                            <span class="text-sm line-clamp-2">{{ $item->keterangan ?? '-' }}</span>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if ($item->status == 'pending')
                                <span class="badge badge-warning badge-sm gap-1">
                                    <x-heroicon-o-clock class="w-3 h-3" />
                                    Pending
                                </span>
                            @elseif ($item->status == 'disetujui')
                                <span class="badge badge-success badge-sm gap-1">
                                    <x-heroicon-o-check-circle class="w-3 h-3" />
                                    Disetujui
                                </span>
                            @elseif ($item->status == 'ditolak')
                                <span class="badge badge-error badge-sm gap-1">
                                    <x-heroicon-o-x-circle class="w-3 h-3" />
                                    Ditolak
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" class="btn btn-ghost btn-xs text-info">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            {{-- Footer: Pagination --}}
            <div class="mt-6 pt-4 border-t border-base-300">
                <div class="flex flex-col gap-4">
                    {{-- Data Info --}}
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Menampilkan {{ $izin->firstItem() ?? 0 }} - {{ $izin->lastItem() ?? 0 }} dari
                        {{ $izin->total() }} data
                    </div>

                    {{-- Pagination Component --}}
                    <x-partials.pagination :paginator="$izin" :perPage="$perPage ?? 10" />
                </div>
            </div>
        </div>
    </div>
</div>
