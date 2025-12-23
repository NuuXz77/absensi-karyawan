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
                                placeholder="Cari karyawan..." />
                        </label>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterStatus || $filterDepartemen)
                                <span
                                    class="badge badge-primary badge-sm">{{ ($filterStatus ? 1 : 0) + ($filterDepartemen ? 1 : 0) }}</span>
                            @endif
                        </label>
                        <div tabindex="0"
                            class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-300 border border-base-100 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Status</span>
                                    </label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="nonaktif">Non-Aktif</option>
                                        <option value="cuti">Cuti</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Departemen</span>
                                    </label>
                                    <select wire:model.live="filterDepartemen" class="select select-bordered select-sm">
                                        <option value="">Semua Departemen</option>
                                        <option value="IT">IT</option>
                                        <option value="HR">HR</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Operations">Operations</option>
                                    </select>
                                </div>
                                <button wire:click="resetFilters" class="btn btn-ghost btn-sm w-full">
                                    Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Create Button -->
                <a wire:navigate href="{{ route('admin.karyawan.create') }}"
                    class="btn btn-primary btn-sm btn-circle gap-2">
                    <x-heroicon-o-plus class="w-5 h-5" />
                </a>
            </div>

            <!-- Table Section -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'NIP', 'field' => 'nip', 'sortable' => true],
                    ['label' => 'Nama Lengkap', 'field' => 'nama_lengkap', 'sortable' => true],
                    ['label' => 'Jabatan', 'field' => 'jabatan', 'sortable' => true],
                    ['label' => 'Departemen', 'field' => 'departemen', 'sortable' => true],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$karyawans" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data karyawan" emptyIcon="heroicon-o-users">
                @foreach ($karyawans as $index => $karyawan)
                    <tr wire:key="karyawan-{{ $karyawan->id }}" class="hover:bg-base-200 transition-colors duration-150"
                        style="overflow: visible !important;">
                        <td>{{ $karyawans->firstItem() + $index }}</td>
                        <td class="font-mono">{{ $karyawan->nip }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="mask mask-squircle w-12 h-12">
                                        @if ($karyawan->foto_karyawan)
                                            <img src="{{ Storage::url($karyawan->foto_karyawan) }}"
                                                alt="{{ $karyawan->nama_lengkap }}" />
                                        @else
                                            <div
                                                class="bg-primary text-primary-content flex items-center justify-center w-full h-full">
                                                <span class="text-lg font-bold">
                                                    {{ substr($karyawan->nama_lengkap, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold">{{ $karyawan->nama_lengkap }}</div>
                                    <div class="text-sm opacity-50">{{ $karyawan->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $karyawan->jabatan }}</td>
                        <td>
                            <span class="badge badge-ghost">{{ $karyawan->departemen }}</span>
                        </td>
                        <td>
                            <span
                                class="badge badge-soft
                                {{ $karyawan->status === 'active' ? 'badge-success' : '' }}
                                {{ $karyawan->status === 'inactive' ? 'badge-error' : '' }}
                            ">
                                {{ ucfirst($karyawan->status) }}
                            </span>
                        </td>
                        <td>
                            <x-partials.dropdown-actions :id="$karyawan->id" />
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer: Pagination -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <div class="flex flex-col gap-4">
                    <!-- Data Info -->
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Menampilkan <span class="font-semibold">{{ $karyawans->firstItem() ?? 0 }}</span>
                        sampai <span class="font-semibold">{{ $karyawans->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold">{{ $karyawans->total() }}</span> data
                    </div>

                    <!-- Pagination Component -->
                    <x-partials.pagination :paginator="$karyawans" :perPage="$perPage" />
                </div>
            </div>
        </div>
    </div>
</div>
