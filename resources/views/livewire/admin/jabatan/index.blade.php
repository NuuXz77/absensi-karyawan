<div>
    <!-- Main Card -->
    <div class="card bg-base-300 border border-base-100" style="overflow: visible !important;">
        <div class="card-body" style="overflow: visible !important;">
            <!-- Top Section: Filters & Actions -->
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                <!-- Left: Search & Filter -->
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <!-- Search Input -->
                    <div class="form-control">
                        <label class="input input-sm">
                            <x-bi-search class="w-3" />
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Cari jabatan..." />
                        </label>
                    </div>

                    <!-- Filter Departemen -->
                    <select wire:model.live="filterDepartemen" class="select select-bordered select-sm">
                        <option value="">Semua Departemen</option>
                        @foreach($departemens as $departemen)
                            <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Status -->
                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>

                    <button wire:click="resetFilters" class="btn btn-sm btn-ghost">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                    </button>
                </div>

                <!-- Right: Create Button -->
                <button wire:click="create" class="btn btn-primary btn-sm gap-2">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    <span class="hidden sm:inline">Tambah Jabatan</span>
                </button>
            </div>

            <!-- Table Section -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Departemen', 'field' => 'departemen_id', 'sortable' => true],
                    ['label' => 'Nama Jabatan', 'field' => 'nama_jabatan', 'sortable' => true],
                    // ['label' => 'Level'],
                    ['label' => 'Jumlah Karyawan'],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$jabatans" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data jabatan" emptyIcon="heroicon-o-briefcase">
                @foreach ($jabatans as $index => $jabatan)
                    <tr wire:key="jabatan-{{ $jabatan->id }}" class="hover:bg-base-200 transition-colors duration-150"
                        style="overflow: visible !important;">
                        <td>{{ $jabatans->firstItem() + $index }}</td>
                        <td>
                            <div class="font-bold">{{ $jabatan->nama_jabatan }}</div>
                            {{-- <div class="text-sm opacity-50">{{ $jabatan->deskripsi ?? '-' }}</div> --}}
                        </td>
                        <td>
                            <span class="badge badge-ghost">
                                {{ $jabatan->departemen->nama_departemen ?? 'Semua Departemen' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-sm 
                                {{ $jabatan->level === 'direktur' ? 'badge-error' : '' }}
                                {{ $jabatan->level === 'manager' ? 'badge-warning' : '' }}
                                {{ $jabatan->level === 'supervisor' ? 'badge-info' : '' }}
                                {{ $jabatan->level === 'staff' ? 'badge-success' : '' }}
                            ">
                                {{ ucfirst($jabatan->level ?? 'Staff') }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-users class="w-4 h-4 opacity-50" />
                                <span class="font-semibold">{{ $jabatan->karyawans_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td>
                            <span
                                class="badge badge-sm
                                {{ $jabatan->status === 'aktif' ? 'badge-success' : '' }}
                                {{ $jabatan->status === 'nonaktif' ? 'badge-error' : '' }}
                            ">
                                {{ ucfirst($jabatan->status) }}
                            </span>
                        </td>
                        <td>
                            <x-partials.dropdown-actions :id="$jabatan->id" />
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer: Pagination -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <div class="flex flex-col gap-4">
                    <!-- Data Info -->
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Menampilkan <span class="font-semibold">{{ $jabatans->firstItem() ?? 0 }}</span>
                        sampai <span class="font-semibold">{{ $jabatans->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold">{{ $jabatans->total() }}</span> data
                    </div>

                    <!-- Pagination Component -->
                    <x-partials.pagination :paginator="$jabatans" :perPage="$perPage" />
                </div>
            </div>
        </div>
    </div>
</div>
