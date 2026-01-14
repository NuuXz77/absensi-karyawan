<div>
    <!-- Toast Notifications -->
    <div class="toast toast-end z-[9999]">
        @if(session('success'))
            <div class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-check class="w-5" />
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

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
                            @if ($filterDepartemen || $filterJabatan)
                                <span class="badge badge-primary badge-sm">{{ ($filterDepartemen ? 1 : 0) + ($filterJabatan ? 1 : 0) }}</span>
                            @endif
                        </label>
                        <div tabindex="0"
                            class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Departemen</span>
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
                                        <span class="label-text">Jabatan</span>
                                    </label>
                                    <select wire:model.live="filterJabatan" class="select select-bordered select-sm">
                                        <option value="">Semua Jabatan</option>
                                        @foreach($jabatans as $jab)
                                            <option value="{{ $jab->id }}">{{ $jab->nama_jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="divider my-2"></div>
                                <button wire:click="resetFilters" class="btn btn-sm btn-block">
                                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                                    Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <x-heroicon-o-information-circle class="w-5 h-5" />
                    <span>Total: <strong>{{ $wajahKaryawans->total() }}</strong> data wajah</span>
                </div>
            </div>

            <!-- Table Section -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Foto Wajah', 'class' => 'text-center'],
                    ['label' => 'ID Card / NIP', 'field' => 'karyawan_id', 'sortable' => false],
                    ['label' => 'Nama Karyawan', 'field' => 'karyawan_id', 'sortable' => false],
                    ['label' => 'Jabatan', 'class' => 'text-center'],
                    ['label' => 'Face Embedding', 'class' => 'text-center'],
                    ['label' => 'Tanggal Upload', 'field' => 'created_at', 'sortable' => true],
                    ['label' => 'Aksi', 'class' => 'text-center'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$wajahKaryawans" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data wajah karyawan" emptyIcon="heroicon-o-photo">
                @foreach ($wajahKaryawans as $index => $wajah)
                    <tr wire:key="wajah-{{ $wajah->id }}" class="hover:bg-base-200 transition-colors duration-150"
                        style="overflow: visible !important;">
                        <td>{{ $wajahKaryawans->firstItem() + $index }}</td>
                        <td class="text-center">
                            <div class="flex justify-center">
                                <div class="avatar">
                                    <div class="w-16 h-16 rounded-lg ring ring-primary ring-offset-base-100 ring-offset-2">
                                        @if($wajah->foto_path && file_exists(public_path('storage/' . $wajah->foto_path)))
                                            <img src="{{ asset('storage/' . $wajah->foto_path) }}" alt="Foto Wajah" class="object-cover" />
                                        @else
                                            <div class="flex items-center justify-center bg-base-200">
                                                <x-heroicon-o-photo class="w-8 h-8 text-gray-400" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-mono text-sm">
                                {{ $wajah->karyawan->id_card ?? $wajah->karyawan->nip ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar placeholder">
                                    <div class="bg-primary text-primary-content rounded-full w-10 h-10">
                                        @if($wajah->karyawan->foto_karyawan && file_exists(public_path('storage/' . $wajah->karyawan->foto_karyawan)))
                                            <img src="{{ asset('storage/' . $wajah->karyawan->foto_karyawan) }}" alt="Avatar" />
                                        @else
                                            <span class="text-sm font-bold">{{ substr($wajah->karyawan->nama_lengkap, 0, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="font-semibold">{{ $wajah->karyawan->nama_lengkap }}</div>
                                    <div class="text-xs opacity-50">{{ $wajah->karyawan->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($wajah->karyawan->jabatan)
                                <div class="badge badge-ghost badge-sm">
                                    {{ $wajah->karyawan->jabatan->nama_jabatan }}
                                </div>
                                @if($wajah->karyawan->departemen)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $wajah->karyawan->departemen->nama_departemen }}
                                    </div>
                                @endif
                            @else
                                <span class="text-xs opacity-50">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($wajah->face_embedding)
                                <div class="tooltip" data-tip="Face embedding tersedia">
                                    <span class="badge badge-success badge-sm gap-1">
                                        <x-heroicon-o-check-circle class="w-3 h-3" />
                                        Ada
                                    </span>
                                </div>
                                <div class="text-xs opacity-50 mt-1">
                                    {{ strlen($wajah->face_embedding) }} chars
                                </div>
                            @else
                                <span class="badge badge-error badge-sm gap-1">
                                    <x-heroicon-o-x-circle class="w-3 h-3" />
                                    Tidak ada
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm">{{ \Carbon\Carbon::parse($wajah->created_at)->format('d M Y') }}</div>
                            <div class="text-xs opacity-50">{{ \Carbon\Carbon::parse($wajah->created_at)->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                </label>
                                <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 border border-base-300">
                                    <li>
                                        <button wire:click="view({{ $wajah->id }})" class="flex items-center gap-2">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            <span>Lihat Detail</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button wire:click="confirmDelete({{ $wajah->id }})" class="flex items-center gap-2 text-error hover:bg-error hover:text-error-content">
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
                        Menampilkan <span class="font-semibold">{{ $wajahKaryawans->firstItem() ?? 0 }}</span>
                        sampai <span class="font-semibold">{{ $wajahKaryawans->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold">{{ $wajahKaryawans->total() }}</span> data
                    </div>

                    <!-- Pagination Component -->
                    <x-partials.pagination :paginator="$wajahKaryawans" :perPage="$perPage" />
                </div>
            </div>
        </div>
    </div>
</div>
