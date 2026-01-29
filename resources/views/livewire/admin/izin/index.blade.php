<div>
    <div class="card bg-base-100 border border-base-300" style="overflow: visible !important;">
        <div class="card-body" style="overflow: visible !important;">
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
                            @if ($filterStatus || $filterKaryawan)
                                <span class="badge badge-primary badge-sm">{{ ($filterStatus ? 1 : 0) + ($filterKaryawan ? 1 : 0) }}</span>
                            @endif
                        </label>
                        <div tabindex="0" class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Status</span></label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text font-semibold">Karyawan</span></label>
                                    <select wire:model.live="filterKaryawan" class="select select-bordered select-sm">
                                        <option value="">Semua Karyawan</option>
                                        @foreach($karyawans as $karyawan)
                                            <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button wire:click="resetFilters" class="btn btn-ghost btn-sm w-full">Reset Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <livewire:admin.izin.modals.detail />
            <livewire:admin.izin.modals.confirm />
            <livewire:admin.izin.modals.delete />

            <!-- Table -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Karyawan'],
                    ['label' => 'Tanggal Mulai', 'field' => 'tanggal_mulai', 'sortable' => true],
                    ['label' => 'Tanggal Selesai', 'field' => 'tanggal_selesai', 'sortable' => true],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$izins" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada pengajuan izin" emptyIcon="heroicon-o-document-text">
                @foreach ($izins as $index => $izin)
                    <tr wire:key="izin-{{ $izin->id }}" class="hover:bg-base-200 transition-colors duration-150" style="overflow: visible !important;">
                        <td>{{ $izins->firstItem() + $index }}</td>
                        <td>
                            <div class="font-semibold">{{ $izin->karyawan->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs opacity-60">{{ $izin->karyawan->departemen->nama_departemen ?? '-' }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                        <td>{{ \Carbon\Carbon::parse($izin->tanggal_selesai)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                        <td>
                            <span class="badge badge-sm badge-soft
                                {{ $izin->status === 'pending' ? 'badge-warning' : '' }}
                                {{ $izin->status === 'disetujui' ? 'badge-success' : '' }}
                                {{ $izin->status === 'ditolak' ? 'badge-error' : '' }}">
                                {{ ucfirst($izin->status) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $customActions = [];
                                if($izin->status === 'pending') {
                                    $customActions[] = [
                                        'method' => 'approve',
                                        'label' => 'Setujui',
                                        'icon' => 'heroicon-o-check',
                                        'class' => 'text-success hover:bg-success hover:text-success-content'
                                    ];
                                    $customActions[] = [
                                        'method' => 'reject',
                                        'label' => 'Tolak',
                                        'icon' => 'heroicon-o-x-mark',
                                        'class' => 'text-warning hover:bg-warning hover:text-warning-content'
                                    ];
                                }
                            @endphp
                            <x-partials.dropdown-actions 
                                :id="$izin->id"
                                :showView="true"
                                :showEdit="false"
                                viewMethod="openDetailModal"
                                detailModalId="modal_detail_izin"
                                deleteModalId="modal_delete_izin"
                                :customActions="$customActions"
                            />
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <x-partials.pagination :paginator="$izins" :perPage="$perPage" />
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast toast-start z-[9999]">
        @if(session()->has('success'))
            <div class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session()->has('error'))
            <div class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5" />
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>
</div>
