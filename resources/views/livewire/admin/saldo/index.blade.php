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
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari karyawan..." />
                        </label>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterTahun)
                                <span class="badge badge-primary badge-sm">1</span>
                            @endif
                        </label>
                        <div tabindex="0" class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Tahun</span></label>
                                    <select wire:model.live="filterTahun" class="select select-bordered select-sm">
                                        <option value="">Semua Tahun</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button wire:click="resetFilters" class="btn btn-ghost btn-sm w-full">Reset Filter</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Create Button -->
                <livewire:admin.saldo.modals.create />
            </div>

            <!-- Modals -->
            <livewire:admin.saldo.modals.edit />
            <livewire:admin.saldo.modals.delete />

            <!-- Table Section -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'NIP', 'field' => 'karyawan_id', 'sortable' => false],
                    ['label' => 'Nama Karyawan', 'sortable' => false],
                    ['label' => 'Tahun', 'field' => 'tahun', 'sortable' => true],
                    ['label' => 'Saldo Izin'],
                    ['label' => 'Saldo Cuti'],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$saldos" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data saldo" emptyIcon="heroicon-o-calculator">
                @foreach ($saldos as $index => $saldo)
                    <tr wire:key="saldo-{{ $saldo->id }}"
                        class="hover:bg-base-200 transition-colors duration-150" style="overflow: visible !important;">
                        <td>{{ $saldos->firstItem() + $index }}</td>
                        <td>
                            <span class="badge badge-soft badge-primary">{{ $saldo->karyawan->nip ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="font-bold">{{ $saldo->karyawan->nama_lengkap ?? '-' }}</div>
                            <div class="text-sm opacity-50">{{ $saldo->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-50" />
                                <span class="font-semibold">{{ $saldo->tahun }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col gap-1">
                                <div class="text-xs text-gray-500">Total: {{ $saldo->total_izin }} hari</div>
                                <div class="badge badge-warning badge-sm">Sisa: {{ $saldo->sisa_izin }} hari</div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col gap-1">
                                <div class="text-xs text-gray-500">Total: {{ $saldo->total_cuti }} hari</div>
                                <div class="badge badge-info badge-sm">Sisa: {{ $saldo->sisa_cuti }} hari</div>
                            </div>
                        </td>
                        <td>
                            <x-partials.dropdown-actions :id="$saldo->id" :showView="false" />
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer: Pagination -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <div class="flex flex-col gap-4">
                    <!-- Data Info -->
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Menampilkan <span class="font-semibold">{{ $saldos->firstItem() ?? 0 }}</span>
                        sampai <span class="font-semibold">{{ $saldos->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold">{{ $saldos->total() }}</span> data
                    </div>

                    <!-- Pagination Component -->
                    <x-partials.pagination :paginator="$saldos" :perPage="$perPage" />
                </div>
            </div>
        </div>
    </div>
</div>
