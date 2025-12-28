<div>
    <!-- Header -->
    <div class="card bg-base-300 border border-base-100 mb-4">
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
                        <div class="badge badge-lg badge-info gap-2">
                            <x-heroicon-o-clock class="w-4 h-4" />
                            {{ $shift->nama_shift ?? 'N/A' }} ({{ $shift->jam_masuk ?? '' }} - {{ $shift->jam_keluar ?? '' }})
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
    <div class="card bg-base-300 border border-base-100">
        <div class="card-body">
            <h3 class="card-title mb-4">
                <x-heroicon-o-user-group class="w-6 h-6" />
                Daftar Karyawan
            </h3>

            @if($jadwals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th class="w-12">#</th>
                                <th>Nama Karyawan</th>
                                <th>NIK</th>
                                <th>Jabatan</th>
                                <th>No. Telp</th>
                                <th>Email</th>
                                <th>Shift</th>
                                <th class="text-center">Status</th>
                                <th class="text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwals as $index => $jadwal)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-10">
                                                    <span class="text-xs">{{ strtoupper(substr($jadwal->karyawan->nama_lengkap, 0, 2)) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">{{ $jadwal->karyawan->nama_lengkap }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-mono text-sm">{{ $jadwal->karyawan->nik }}</span>
                                    </td>
                                    <td>
                                        <div class="badge badge-ghost">{{ $jadwal->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                                    </td>
                                    <td>{{ $jadwal->karyawan->no_telepon ?? '-' }}</td>
                                    <td>{{ $jadwal->karyawan->email ?? '-' }}</td>
                                    <td>
                                        @if($editingJadwalId === $jadwal->id)
                                            <select wire:model="editShiftId" class="select select-bordered select-sm w-full max-w-xs">
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
                                        @if($jadwal->karyawan->status === 'active')
                                            <div class="badge badge-success">Aktif</div>
                                        @else
                                            <div class="badge badge-error">Non-Aktif</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="flex gap-1 justify-center">
                                            @if($editingJadwalId === $jadwal->id)
                                                <button wire:click="updateJadwal({{ $jadwal->id }})" 
                                                    class="btn btn-success btn-xs"
                                                    title="Simpan">
                                                    <x-heroicon-o-check class="w-4 h-4" />
                                                </button>
                                                <button wire:click="cancelEdit" 
                                                    class="btn btn-ghost btn-xs"
                                                    title="Batal">
                                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                                </button>
                                            @else
                                                <button wire:click="editJadwal({{ $jadwal->id }})" 
                                                    class="btn btn-warning btn-xs"
                                                    title="Edit Shift">
                                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                                </button>
                                                <button wire:click="deleteJadwal({{ $jadwal->id }})" 
                                                    wire:confirm="Apakah Anda yakin ingin menghapus jadwal ini?"
                                                    class="btn btn-error btn-xs"
                                                    title="Hapus">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    <span>Tidak ada karyawan dijadwalkan untuk shift ini pada tanggal tersebut.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Additional Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <!-- Shift Info -->
        <div class="card bg-base-300 border border-base-100">
            <div class="card-body">
                <h4 class="card-title text-lg">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    Informasi Shift
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-semibold">Nama Shift:</span>
                        <span>{{ $shift->nama_shift ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Jam Masuk:</span>
                        <span>{{ $shift->jam_masuk ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Jam Keluar:</span>
                        <span>{{ $shift->jam_keluar ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold">Status:</span>
                        <span>
                            @if($shift && $shift->status === 'active')
                                <div class="badge badge-success badge-sm">Aktif</div>
                            @else
                                <div class="badge badge-error badge-sm">Non-Aktif</div>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departemen Info -->
        <div class="card bg-base-300 border border-base-100">
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
                        <span class="font-semibold">Status:</span>
                        <span>
                            @if($departemen && $departemen->status === 'active')
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
