<div>
    <!-- Main Card -->
    <div class="card bg-base-300 border border-base-100" style="overflow: visible !important;">
        <div class="card-body" style="overflow: visible !important;">
            <!-- Top Section -->
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="form-control">
                        <label class="input input-sm">
                            <x-bi-search class="w-3" />
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari shift..." />
                        </label>
                    </div>
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterStatus)
                                <span class="badge badge-primary badge-sm">1</span>
                            @endif
                        </label>
                        <div tabindex="0" class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-300 border border-base-100 mt-2">
                            <div class="space-y-3">
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
                <livewire:admin.shift.modals.create />
            </div>

            <!-- Modals -->
            <livewire:admin.shift.modals.edit />
            <livewire:admin.shift.modals.delete />

            <!-- Table -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Nama Shift', 'field' => 'nama_shift', 'sortable' => true],
                    ['label' => 'Jam Masuk', 'field' => 'jam_masuk', 'sortable' => true],
                    ['label' => 'Jam Pulang', 'field' => 'jam_pulang', 'sortable' => true],
                    ['label' => 'Toleransi', 'field' => 'toleransi_menit', 'sortable' => true],
                    ['label' => 'Jadwal Kerja'],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$shifts" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data shift" emptyIcon="heroicon-o-clock">
                @foreach ($shifts as $index => $shift)
                    <tr wire:key="shift-{{ $shift->id }}" class="hover:bg-base-200 transition-colors duration-150" style="overflow: visible !important;">
                        <td>{{ $shifts->firstItem() + $index }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="bg-info/20 p-2 rounded-lg">
                                    <x-heroicon-o-clock class="w-4 h-4 text-info" />
                                </div>
                                <span class="font-semibold">{{ $shift->nama_shift }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-outline badge-success">{{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}</span>
                        </td>
                        <td>
                            <span class="badge badge-outline badge-error">{{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }}</span>
                        </td>
                        <td>
                            <span class="badge badge-warning badge-soft badge-sm">{{ $shift->toleransi_menit }} menit</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-50" />
                                <span class="font-semibold">{{ $shift->jadwal_kerja_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-sm badge-soft {{ $shift->status === 'active' ? 'badge-success' : 'badge-error' }}">
                                {{ ucfirst($shift->status) }}
                            </span>
                        </td>
                        <td>
                            <x-partials.dropdown-actions :id="$shift->id" />
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <div class="flex flex-col gap-4">
                    <!-- Data Info -->
                    <div class="text-sm text-gray-600 text-center sm:text-left">
                        Menampilkan <span class="font-semibold">{{ $shifts->firstItem() ?? 0 }}</span>
                        sampai <span class="font-semibold">{{ $shifts->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold">{{ $shifts->total() }}</span> data
                    </div>

                    <!-- Pagination Component -->
                    <x-partials.pagination :paginator="$shifts" :perPage="$perPage" />
                </div>
            </div>
        </div>
    </div>
</div>
