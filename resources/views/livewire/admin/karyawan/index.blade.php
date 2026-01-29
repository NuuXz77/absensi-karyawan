<div>
    <!-- Toast Notifications -->
    <x-partials.toast :success="session('success')" :error="session('error')" />

    <!-- Main Card -->
    <div class="card bg-base-100 border border-base-300" style="overflow: visible !important;">
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
                            @if ($filterStatus || $filterDepartemen || $filterJabatan)
                                <span
                                    class="badge badge-primary badge-sm">{{ ($filterStatus ? 1 : 0) + ($filterDepartemen ? 1 : 0) + ($filterJabatan ? 1 : 0) }}</span>
                            @endif
                        </label>
                        <div tabindex="0"
                            class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Status</span>
                                    </label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Non-Aktif</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Departemen</span>
                                    </label>
                                    <select wire:model.live="filterDepartemen" class="select select-bordered select-sm">
                                        <option value="">Semua Departemen</option>
                                        @foreach($departemens as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Jabatan</span>
                                    </label>
                                    <select wire:model.live="filterJabatan" class="select select-bordered select-sm">
                                        <option value="">Semua Jabatan</option>
                                        @foreach($jabatans as $jab)
                                            <option value="{{ $jab->id }}">{{ $jab->nama_jabatan }}</option>
                                        @endforeach
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
                    ['label' => 'ID Card / NIP', 'field' => 'id_card', 'sortable' => true],
                    ['label' => 'Nama Lengkap', 'field' => 'nama_lengkap', 'sortable' => true],
                    ['label' => 'Jabatan', 'class' => 'text-center'],
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
                        <td>
                            <div class="font-mono text-sm">
                                <div class="font-semibold">{{ $karyawan->id_card }}</div>
                                <div class="text-xs opacity-60">{{ $karyawan->nip }}</div>
                            </div>
                        </td>
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
                        <td class="text-center">
                            @if($karyawan->jabatan)
                                <div class="font-semibold">{{ $karyawan->jabatan->nama_jabatan }}</div>
                                @if($karyawan->departemen)
                                    <span class="badge badge-ghost badge-sm mt-1">{{ $karyawan->departemen->nama_departemen }}</span>
                                @endif
                            @else
                                <span class="text-xs opacity-50">-</span>
                            @endif
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
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                </label>
                                <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 border border-base-300 z-10">
                                    <li>
                                        <a href="{{ route('admin.karyawan.detail', $karyawan->id) }}" wire:navigate class="flex items-center gap-2">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            <span>Lihat Detail</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.karyawan.edit', $karyawan->id) }}" wire:navigate class="flex items-center gap-2">
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                            <span>Edit</span>
                                        </a>
                                    </li>
                                    <li>
                                        <button wire:click="$dispatch('open-delete-modal', { karyawanId: {{ $karyawan->id }} })" class="flex items-center gap-2 text-error hover:bg-error hover:text-error-content">
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

    <!-- Delete Modal -->
    <livewire:admin.karyawan.modals.delete />
</div>
