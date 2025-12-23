<div>
    <div class="card bg-base-300 border border-base-100">
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
                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <select wire:model.live="filterKaryawan" class="select select-bordered select-sm">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    <button wire:click="resetFilters" class="btn btn-sm btn-ghost">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Table -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Karyawan'],
                    ['label' => 'Tanggal Mulai', 'field' => 'tanggal_mulai', 'sortable' => true],
                    ['label' => 'Tanggal Selesai', 'field' => 'tanggal_selesai', 'sortable' => true],
                    ['label' => 'Durasi'],
                    ['label' => 'Alasan'],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$cutis" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada pengajuan cuti" emptyIcon="heroicon-o-calendar-days">
                @foreach ($cutis as $index => $cuti)
                    <tr wire:key="cuti-{{ $cuti->id }}" class="hover:bg-base-200">
                        <td>{{ $cutis->firstItem() + $index }}</td>
                        <td>
                            <div class="font-semibold">{{ $cuti->karyawan->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs opacity-60">{{ $cuti->karyawan->departemen ?? '-' }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                        <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                        <td>
                            @php
                                $start = \Carbon\Carbon::parse($cuti->tanggal_mulai);
                                $end = \Carbon\Carbon::parse($cuti->tanggal_selesai);
                                $durasi = $start->diffInDays($end) + 1;
                            @endphp
                            <span class="badge badge-info badge-sm">{{ $durasi }} hari</span>
                        </td>
                        <td>
                            <div class="max-w-xs truncate" title="{{ $cuti->alasan }}">{{ $cuti->alasan }}</div>
                        </td>
                        <td>
                            <span class="badge badge-sm 
                                {{ $cuti->status === 'pending' ? 'badge-warning' : '' }}
                                {{ $cuti->status === 'disetujui' ? 'badge-success' : '' }}
                                {{ $cuti->status === 'ditolak' ? 'badge-error' : '' }}">
                                {{ ucfirst($cuti->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($cuti->status === 'pending')
                                <div class="flex gap-1 justify-center">
                                    <button wire:click="approve({{ $cuti->id }})" class="btn btn-success btn-xs" title="Setujui">
                                        <x-heroicon-o-check class="w-4 h-4" />
                                    </button>
                                    <button wire:click="reject({{ $cuti->id }})" class="btn btn-error btn-xs" title="Tolak">
                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                    </button>
                                </div>
                            @else
                                <div class="text-xs opacity-60">
                                    {{ $cuti->disetujuiOleh->karyawan->nama_lengkap ?? 'Admin' }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>

            <!-- Footer -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <x-partials.pagination :paginator="$cutis" :perPage="$perPage" />
            </div>
        </div>
    </div>
</div>
