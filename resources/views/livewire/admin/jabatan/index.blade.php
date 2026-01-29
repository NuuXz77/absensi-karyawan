<div>
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
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari jabatan..." />
                        </label>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterDepartemen || $filterStatus)
                                <span class="badge badge-primary badge-sm">{{ ($filterDepartemen ? 1 : 0) + ($filterStatus ? 1 : 0) }}</span>
                            @endif
                        </label>
                        <div tabindex="0" class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Departemen</span></label>
                                    <select wire:model.live="filterDepartemen" class="select select-bordered select-sm">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemens as $departemen)
                                            <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Status</span></label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Non-Aktif</option>
                                    </select>
                                </div>
                                <button wire:click="resetFilters" class="btn btn-ghost btn-sm w-full">Reset Filter</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Create Button -->
                <livewire:admin.jabatan.modals.create />
            </div>

            <!-- Modals -->
            <livewire:admin.jabatan.modals.edit />
            <livewire:admin.jabatan.modals.delete />

            <!-- Table Section -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Kode', 'field' => 'kode_jabatan', 'sortable' => true],
                    ['label' => 'Nama Jabatan', 'field' => 'nama_jabatan', 'sortable' => true],
                    ['label' => 'Departemen', 'field' => 'departemen_id', 'sortable' => true],
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
                            <span class="badge badge-ghost font-mono">{{ $jabatan->kode_jabatan }}</span>
                        </td>
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
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-users class="w-4 h-4 opacity-50" />
                                <span class="font-semibold">{{ $jabatan->karyawans_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td>
                            <span
                                class="badge badge-soft badge-sm
                                {{ $jabatan->status === 'active' ? 'badge-success' : '' }}
                                {{ $jabatan->status === 'inactive' ? 'badge-error' : '' }}
                            ">
                                {{ ucfirst($jabatan->status) }}
                            </span>
                        </td>
                        <td>
                            <x-partials.dropdown-actions :id="$jabatan->id" editModalId="modal_edit_jabatan"
                                deleteModalId="modal_delete_jabatan"/>
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
