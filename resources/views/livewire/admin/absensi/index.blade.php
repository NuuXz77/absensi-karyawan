<div>
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <!-- Top Section -->
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="form-control">
                        <label class="input input-sm">
                            <x-bi-search class="w-3" />
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." />
                        </label>
                    </div>
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterTanggal || $filterStatus || $filterKaryawan)
                                <span
                                    class="badge badge-primary badge-sm">{{ ($filterTanggal ? 1 : 0) + ($filterStatus ? 1 : 0) + ($filterKaryawan ? 1 : 0) }}</span>
                            @endif
                        </label>
                        <div tabindex="0"
                            class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Tanggal</span></label>
                                    <input type="date" wire:model.live="filterTanggal"
                                        class="input input-bordered input-sm" />
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Status</span></label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="hadir">Hadir</option>
                                        <option value="terlambat">Terlambat</option>
                                        <option value="alpha">Alpha</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Karyawan</span></label>
                                    <select wire:model.live="filterKaryawan" class="select select-bordered select-sm">
                                        <option value="">Semua Karyawan</option>
                                        @foreach ($karyawans as $karyawan)
                                            <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button wire:click="resetFilters" class="btn btn-ghost btn-sm w-full">Reset
                                    Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <livewire:admin.absensi.modals.export-excel />
                    <livewire:admin.absensi.modals.export-pdf />
                </div>
            </div>

            <!-- Table -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Tanggal', 'field' => 'tanggal', 'sortable' => true],
                    ['label' => 'Karyawan', 'field' => 'karyawan_id', 'sortable' => true],
                    ['label' => 'Waktu Masuk', 'field' => 'waktu_masuk', 'sortable' => true],
                    ['label' => 'Waktu Keluar', 'field' => 'waktu_keluar', 'sortable' => true],
                    ['label' => 'Lokasi'],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center w-24'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$absensis" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data absensi" emptyIcon="heroicon-o-clipboard-document-check">
                @foreach ($absensis as $index => $absensi)
                    <tr wire:key="absensi-{{ $absensi->id }}" class="hover:bg-base-200">
                        <td>{{ $absensis->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                        <td>
                            <div class="font-semibold">{{ $absensi->karyawan->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs opacity-60">{{ $absensi->karyawan->nip ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-success badge-sm">
                                {{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-error badge-sm">
                                {{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') : '-' }}
                            </span>
                        </td>
                        <td>{{ $absensi->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>
                            <span
                                class="badge badge-sm 
                                {{ $absensi->status === 'hadir' ? 'badge-success' : '' }}
                                {{ $absensi->status === 'terlambat' ? 'badge-warning' : '' }}
                                {{ $absensi->status === 'alpha' ? 'badge-error' : '' }}">
                                {{ ucfirst($absensi->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                </label>
                                <ul tabindex="0"
                                    class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 border border-base-300 z-10">
                                    <li>
                                        <a href="{{ route('admin.absensi.detail', $absensi->id) }}" wire:navigate
                                            class="flex items-center gap-2">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            <span>Lihat Detail</span>
                                        </a>
                                    </li>
                                    <li>
                                        <button
                                            wire:click="$dispatch('open-delete-modal', { absensiId: {{ $absensi->id }} })"
                                            class="flex items-center gap-2 text-error hover:bg-error hover:text-error-content">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                            <span>Hapus</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <x-partials.pagination :paginator="$absensis" :perPage="$perPage" />
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <livewire:admin.absensi.modals.delete />
</div>
