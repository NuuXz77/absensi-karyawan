<div>
    <!-- Header -->
    <div class="card bg-base-100 border border-base-300 mb-4">
        <div class="card-body">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <a href="{{ route('admin.jadwal.index') }}" wire:navigate class="btn btn-ghost btn-sm btn-circle">
                            <x-heroicon-o-arrow-left class="w-5 h-5" />
                        </a>
                        <h2 class="text-2xl font-bold">Detail Jadwal</h2>
                    </div>
                    <div class="flex flex-wrap gap-2 items-center text-sm">
                        <div class="badge badge-lg badge-primary gap-2">
                            <x-heroicon-o-calendar class="w-4 h-4" />
                            {{ $formattedDate }}
                        </div>
                        <div class="badge badge-lg badge-accent gap-2">
                            <x-heroicon-o-building-office class="w-4 h-4" />
                            {{ $departemen->nama_departemen ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="stats shadow">
                    <div class="stat py-3 px-4">
                        <div class="stat-title text-xs">Total Karyawan</div>
                        <div class="stat-value text-2xl">{{ $jadwals->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success mb-4">
            <x-heroicon-o-check-circle class="w-6 h-6" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mb-4">
            <x-heroicon-o-x-circle class="w-6 h-6" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Karyawan List -->
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                <h3 class="card-title">
                    <x-heroicon-o-user-group class="w-6 h-6" />
                    Daftar Karyawan
                </h3>

                <!-- Filters -->
                <div class="flex gap-2">
                    <div class="form-control">
                        <label class="label py-0 pb-1">
                            <span class="label-text text-xs">Filter Lokasi</span>
                        </label>
                        <select wire:model.live="filterLokasi" class="select select-bordered select-sm w-full md:w-48">
                            <option value="">Semua Lokasi</option>
                            @foreach ($allLokasis as $lok)
                                <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-0 pb-1">
                            <span class="label-text text-xs">Filter Shift</span>
                        </label>
                        <select wire:model.live="filterShift" class="select select-bordered select-sm w-full md:w-48">
                            <option value="">Semua Shift</option>
                            @foreach ($allShifts as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_shift }}
                                    ({{ $s->jam_masuk }}-{{ $s->jam_pulang }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <x-partials.table :columns="[
                ['label' => '#', 'class' => 'w-12'],
                ['label' => 'Nama Karyawan'],
                ['label' => 'Jabatan'],
                ['label' => 'Shift'],
                ['label' => 'Status', 'class' => 'text-center'],
                ['label' => 'Aksi', 'class' => 'text-center w-32'],
            ]" :data="$jadwals" emptyMessage="Tidak ada karyawan dijadwalkan"
                emptyIcon="heroicon-o-user-group">
                @foreach ($jadwals as $index => $jadwal)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="mask mask-squircle w-12 h-12">
                                        @if ($jadwal->karyawan->foto_karyawan)
                                            <img src="{{ Storage::url($jadwal->karyawan->foto_karyawan) }}"
                                                alt="{{ $jadwal->karyawan->nama_lengkap }}" />
                                        @else
                                            <div
                                                class="bg-primary text-primary-content flex items-center justify-center w-full h-full">
                                                <span class="text-lg font-bold">
                                                    {{ substr($jadwal->karyawan->nama_lengkap, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold">{{ $jadwal->karyawan->nama_lengkap }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="badge badge-ghost">{{ $jadwal->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                        </td>
                        <td>
                            @if ($editingJadwalId === $jadwal->id)
                                <select wire:model="editShiftId"
                                    class="select select-bordered select-sm w-full max-w-xs">
                                    @foreach ($allShifts as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama_shift }}</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="badge badge-info gap-2">
                                    <x-heroicon-o-clock class="w-3 h-3" />
                                    {{ $jadwal->shift->nama_shift }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($jadwal->karyawan->status === 'active')
                                <div class="badge badge-success">Aktif</div>
                            @else
                                <div class="badge badge-error">Non-Aktif</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($editingJadwalId === $jadwal->id)
                                <div class="flex gap-1 justify-center">
                                    <button wire:click="updateJadwal({{ $jadwal->id }})"
                                        class="btn btn-success btn-xs" title="Simpan">
                                        <x-heroicon-o-check class="w-4 h-4" />
                                    </button>
                                    <button wire:click="cancelEdit" class="btn btn-ghost btn-xs" title="Batal">
                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                    </button>
                                </div>
                            @else
                                <x-partials.dropdown-actions :id="$jadwal->id" :showView="false" editMethod="editJadwal"
                                    deleteMethod="deleteJadwal" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <!-- Shifts Summary -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h4 class="card-title text-lg">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    Ringkasan Shift
                </h4>
                <div class="space-y-3 text-sm">
                    @php
                        $shiftGroups = $jadwals->groupBy('shift_id');
                    @endphp
                    @foreach ($shiftGroups as $shiftId => $jadwalsInShift)
                        @php
                            $shiftInfo = $jadwalsInShift->first()->shift;
                        @endphp
                        <div class="p-3 bg-base-200/50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-base">{{ $shiftInfo->nama_shift }}</span>
                                <span class="badge badge-info gap-1">
                                    <x-heroicon-o-user-group class="w-3 h-3" />
                                    {{ $jadwalsInShift->count() }} karyawan
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-base-content/70">
                                <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                <span>{{ $shiftInfo->jam_masuk }} - {{ $shiftInfo->jam_pulang }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if ($jadwals->isEmpty())
                        <div class="text-center py-4 text-base-content/50">
                            <x-heroicon-o-information-circle class="w-8 h-8 mx-auto mb-2 opacity-50" />
                            <p class="text-sm">Tidak ada data shift</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Departemen Info -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h4 class="card-title text-lg">
                    <x-heroicon-o-building-office class="w-5 h-5" />
                    Informasi Departemen
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-semibold">Nama Departemen:</span>
                        <span>{{ $departemen->nama_departemen ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Kode:</span>
                        <span class="font-mono">{{ $departemen->kode_departemen ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Total Karyawan Dijadwalkan:</span>
                        <span class="badge badge-primary">{{ $jadwals->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Total Shift Aktif:</span>
                        <span class="badge badge-info">{{ $jadwals->pluck('shift_id')->unique()->count() }}
                            shift</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Status:</span>
                        <span>
                            @if ($departemen && $departemen->status === 'active')
                                <div class="badge badge-success badge-sm">Aktif</div>
                            @else
                                <div class="badge badge-error badge-sm">Non-Aktif</div>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
