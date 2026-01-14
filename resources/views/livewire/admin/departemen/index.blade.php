<div>
    <!-- Main Card -->
    <div class="card bg-base-100 border border-base-300" style="overflow: visible !important;">
        <div class="card-body" style="overflow: visible !important;">
            <!-- Top Section: Filters & Actions -->
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                <!-- Left: Search & Filter -->
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <!-- Search Input -->
                    <label class="input input-sm">
                        <x-bi-search class="w-3" />
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari departemen..." />
                    </label>

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
                <livewire:admin.departemen.modals.create />
            </div>

            <!-- Modals -->
            <livewire:admin.departemen.modals.edit />
            <livewire:admin.departemen.modals.delete />

            <!-- Table Section -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Kode', 'field' => 'kode_departemen', 'sortable' => true],
                    ['label' => 'Nama Departemen', 'field' => 'nama_departemen', 'sortable' => true],
                    ['label' => 'Jumlah Karyawan'],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$departemens" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data departemen" emptyIcon="heroicon-o-building-office">
                @foreach ($departemens as $index => $departemen)
                    <tr wire:key="departemen-{{ $departemen->id }}"
                        class="hover:bg-base-200 transition-colors duration-150" style="overflow: visible !important;">
                        <td>{{ $departemens->firstItem() + $index }}</td>
                        <td>
                            <span class="badge badge-soft badge-primary">{{ $departemen->kode_departemen }}</span>
                        </td>
                        <td>
                            <div class="font-bold">{{ $departemen->nama_departemen }}</div>
                            {{-- <div class="text-sm opacity-50">{{ $departemen->deskripsi ?? '-' }}</div> --}}
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-users class="w-4 h-4 opacity-50" />
                                <span class="font-semibold">{{ $departemen->karyawans_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td>
                            <span
                                class="badge badge-soft badge-sm
                                {{ $departemen->status === 'active' ? 'badge-success' : '' }}
                                {{ $departemen->status === 'inactive' ? 'badge-error' : '' }}
                            ">
                                {{ ucfirst($departemen->status) }}
                            </span>
                        </td>
                        <td>
                            <x-partials.dropdown-actions :id="$departemen->id" />
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer: Pagination -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <div class="flex flex-col gap-4">
                    <!-- Data Info -->
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Menampilkan <span class="font-semibold">{{ $departemens->firstItem() ?? 0 }}</span>
                        sampai <span class="font-semibold">{{ $departemens->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold">{{ $departemens->total() }}</span> data
                    </div>

                    <!-- Pagination Component -->
                    <x-partials.pagination :paginator="$departemens" :perPage="$perPage" />
                </div>
            </div>
        </div>
    </div>
</div>
