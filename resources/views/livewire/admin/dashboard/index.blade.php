<div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Karyawan -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Total Karyawan</p>
                        <h2 class="text-3xl font-bold">{{ $totalKaryawan }}</h2>
                    </div>
                    <div class="bg-primary/20 text-primary p-3 rounded-lg">
                        <x-heroicon-o-users class="w-8 h-8" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Karyawan Aktif -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Karyawan Aktif</p>
                        <h2 class="text-3xl font-bold">{{ $karyawanAktif }}</h2>
                    </div>
                    <div class="bg-success/20 text-success p-3 rounded-lg">
                        <x-heroicon-o-user-circle class="w-8 h-8" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Hadir Hari Ini</p>
                        <h2 class="text-3xl font-bold">{{ $hadirHariIni }}</h2>
                    </div>
                    <div class="bg-info/20 text-info p-3 rounded-lg">
                        <x-heroicon-o-check-circle class="w-8 h-8" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Izin & Cuti -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Izin & Cuti Hari Ini</p>
                        <h2 class="text-3xl font-bold">{{ $izinHariIni + $cutiAktif }}</h2>
                    </div>
                    <div class="bg-warning/20 text-warning p-3 rounded-lg">
                        <x-heroicon-o-calendar class="w-8 h-8" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Pending Approvals -->
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Recent Absensi -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-primary" />
                    Absensi Terbaru
                </h3>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Waktu</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAbsensi as $absensi)
                                <tr>
                                    <td>{{ $absensi->karyawan->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $absensi->waktu_masuk ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-sm 
                                            {{ $absensi->status === 'hadir' ? 'badge-success' : '' }}
                                            {{ $absensi->status === 'terlambat' ? 'badge-warning' : '' }}
                                            {{ $absensi->status === 'alpha' ? 'badge-error' : '' }}">
                                            {{ ucfirst($absensi->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500">Belum ada absensi hari ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="card-title flex items-center gap-2">
                    <x-heroicon-o-bell-alert class="w-5 h-5 text-warning" />
                    Menunggu Persetujuan
                </h3>
                <div class="space-y-3">
                    <!-- Pending Izin -->
                    <div class="flex items-center justify-between p-4 bg-base-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-warning/20 p-2 rounded-lg">
                                <x-heroicon-o-document-text class="w-6 h-6 text-warning" />
                            </div>
                            <div>
                                <p class="font-semibold">Permohonan Izin</p>
                                <p class="text-sm text-gray-500">{{ $pendingIzin }} izin menunggu</p>
                            </div>
                        </div>
                        <a wire:navigate href="{{ route('admin.izin.index') }}" class="btn btn-sm btn-ghost">
                            Lihat <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>

                    <!-- Pending Cuti -->
                    <div class="flex items-center justify-between p-4 bg-base-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="bg-info/20 p-2 rounded-lg">
                                <x-heroicon-o-calendar-days class="w-6 h-6 text-info" />
                            </div>
                            <div>
                                <p class="font-semibold">Permohonan Cuti</p>
                                <p class="text-sm text-gray-500">{{ $pendingCuti }} cuti menunggu</p>
                            </div>
                        </div>
                        <a wire:navigate href="{{ route('admin.cuti.index') }}" class="btn btn-sm btn-ghost">
                            Lihat <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>

                    <!-- Quick Access -->
                    <div class="divider">Quick Access</div>
                    <div class="grid grid-cols-2 gap-2">
                        <a wire:navigate href="{{ route('admin.karyawan.index') }}" class="btn btn-sm btn-outline">
                            <x-heroicon-o-users class="w-4 h-4" />
                            Karyawan
                        </a>
                        <a wire:navigate href="{{ route('admin.jadwal.index') }}" class="btn btn-sm btn-outline">
                            <x-heroicon-o-calendar class="w-4 h-4" />
                            Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
