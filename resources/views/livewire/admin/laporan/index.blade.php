<div>
    <div class="space-y-6">
        <!-- Header Section dengan Filter dan Action Button -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
                    <!-- Title -->
                    <div>
                        <h2 class="text-2xl font-bold flex items-center gap-3">
                            <x-heroicon-o-document-chart-bar class="w-8 h-8 text-primary" />
                            Laporan Keseluruhan Data
                        </h2>
                        <p class="text-sm opacity-60 mt-1">Statistik dan laporan data sistem absensi</p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <button wire:click="exportAllReports" class="btn btn-primary btn-sm gap-2">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        Cetak Semua Laporan
                    </button>
                </div>
                
                <!-- Filter Section -->
                <div class="divider my-2"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Tanggal Awal -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">
                                <x-heroicon-o-calendar class="w-4 h-4 inline mr-1" />
                                Tanggal Awal
                            </span>
                        </label>
                        <input type="date" wire:model.live="tanggal_awal" class="input input-bordered input-sm" />
                    </div>
                    
                    <!-- Tanggal Akhir -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">
                                <x-heroicon-o-calendar class="w-4 h-4 inline mr-1" />
                                Tanggal Akhir
                            </span>
                        </label>
                        <input type="date" wire:model.live="tanggal_akhir" class="input input-bordered input-sm" />
                    </div>
                    
                    <!-- Departemen Filter -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">
                                <x-heroicon-o-building-office class="w-4 h-4 inline mr-1" />
                                Departemen
                            </span>
                        </label>
                        <select wire:model.live="departemen_id" class="select select-bordered select-sm">
                            <option value="">Semua Departemen</option>
                            @foreach($departemens as $departemen)
                                <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Jabatan Filter -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">
                                <x-heroicon-o-briefcase class="w-4 h-4 inline mr-1" />
                                Jabatan
                            </span>
                        </label>
                        <select wire:model.live="jabatan_id" class="select select-bordered select-sm">
                            <option value="">Semua Jabatan</option>
                            @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Reset Button -->
                <div class="flex justify-end mt-2">
                    <button wire:click="resetFilters" class="btn btn-ghost btn-sm gap-2">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Grid (4 columns, responsive) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Karyawan -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'karyawan' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Karyawan</p>
                            <h3 class="text-3xl font-bold text-primary mt-1">{{ $totalKaryawan }}</h3>
                            <div class="flex gap-3 mt-2 text-xs">
                                <span class="badge badge-success badge-xs badge-soft">Aktif: {{ $karyawanAktif }}</span>
                                <span class="badge badge-xs badge-soft">Non-Aktif: {{ $karyawanNonAktif }}</span>
                            </div>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-primary/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-users class="text-primary w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Absensi -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'absensi' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Absensi</p>
                            <h3 class="text-3xl font-bold text-success mt-1">{{ $totalAbsensi }}</h3>
                            <div class="mt-2 text-xs">
                                <span class="badge badge-info badge-xs badge-soft">Hari Ini: {{ $absensiHariIni }}</span>
                            </div>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-success/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-clipboard-document-check class="text-success w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Total Cuti -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'cuti' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Cuti</p>
                            <h3 class="text-3xl font-bold text-info mt-1">{{ $totalCuti }}</h3>
                            <div class="flex gap-2 mt-2 text-xs">
                                <span class="badge badge-warning badge-xs badge-soft">Tertunda: {{ $cutiDiajukan }}</span>
                                <span class="badge badge-success badge-xs badge-soft">Disetujui: {{ $cutiDisetujui }}</span>
                            </div>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-info/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-calendar-days class="text-info w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Total Izin -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'izin' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Izin</p>
                            <h3 class="text-3xl font-bold text-warning mt-1">{{ $totalIzin }}</h3>
                            <div class="flex gap-2 mt-2 text-xs">
                                <span class="badge badge-warning badge-xs badge-soft">Tertunda: {{ $izinDiajukan }}</span>
                                <span class="badge badge-success badge-xs badge-soft">Disetujui: {{ $izinDisetujui }}</span>
                            </div>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-warning/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-document-text class="text-warning w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Total Departemen -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'departemen' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Departemen</p>
                            <h3 class="text-3xl font-bold text-secondary mt-1">{{ $totalDepartemen }}</h3>
                            <p class="text-xs opacity-50 mt-2">Master Data</p>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-secondary/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-building-office class="text-secondary w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Total Jabatan -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'jabatan' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Jabatan</p>
                            <h3 class="text-3xl font-bold text-accent mt-1">{{ $totalJabatan }}</h3>
                            <p class="text-xs opacity-50 mt-2">Master Data</p>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-accent/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-briefcase class="text-accent w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 7: Total Lokasi -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'lokasi' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Lokasi</p>
                            <h3 class="text-3xl font-bold text-error mt-1">{{ $totalLokasi }}</h3>
                            <p class="text-xs opacity-50 mt-2">Master Data</p>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-error/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-map-pin class="text-error w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 8: Total Shift -->
            <div class="card bg-base-100 border border-base-300 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                 wire:click="$dispatch('open-detail-modal', { type: 'shift' })">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs opacity-60 uppercase font-semibold">Total Shift</p>
                            <h3 class="text-3xl font-bold text-base-200 mt-1">{{ $totalShift }}</h3>
                            <p class="text-xs opacity-50 mt-2">Master Data</p>
                        </div>
                        <div class="avatar placeholder">
                            <div class="bg-base-200/20 rounded-full w-16 flex items-center justify-center">
                                <x-heroicon-o-clock class="text-base-200 w-8 h-8" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Important Reports Section -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="text-xl font-bold flex items-center gap-2 mb-4">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-primary" />
                    Laporan Penting Lainnya
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Laporan Absensi Harian -->
                    <div class="card bg-base-200 border border-base-300 hover:shadow-lg transition-all cursor-pointer">
                        <div class="card-body">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-calendar class="w-10 h-10 text-primary flex-shrink-0" />
                                <div>
                                    <h4 class="font-semibold">Absensi Harian</h4>
                                    <p class="text-xs opacity-60 mt-1">Rekap kehadiran per hari</p>
                                    <button class="btn btn-primary btn-xs mt-3">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Keterlambatan -->
                    <div class="card bg-base-200 border border-base-300 hover:shadow-lg transition-all cursor-pointer">
                        <div class="card-body">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-clock class="w-10 h-10 text-warning flex-shrink-0" />
                                <div>
                                    <h4 class="font-semibold">Keterlambatan</h4>
                                    <p class="text-xs opacity-60 mt-1">Daftar karyawan terlambat</p>
                                    <button class="btn btn-warning btn-xs mt-3">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Kinerja Departemen -->
                    <div class="card bg-base-200 border border-base-300 hover:shadow-lg transition-all cursor-pointer">
                        <div class="card-body">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-chart-pie class="w-10 h-10 text-success flex-shrink-0" />
                                <div>
                                    <h4 class="font-semibold">Kinerja Departemen</h4>
                                    <p class="text-xs opacity-60 mt-1">Analisis per departemen</p>
                                    <button class="btn btn-success btn-xs mt-3">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Cuti & Izin -->
                    <div class="card bg-base-200 border border-base-300 hover:shadow-lg transition-all cursor-pointer">
                        <div class="card-body">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-document-duplicate class="w-10 h-10 text-info flex-shrink-0" />
                                <div>
                                    <h4 class="font-semibold">Cuti & Izin</h4>
                                    <p class="text-xs opacity-60 mt-1">Status permohonan</p>
                                    <button class="btn btn-info btn-xs mt-3">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Lembur -->
                    <div class="card bg-base-200 border border-base-300 hover:shadow-lg transition-all cursor-pointer">
                        <div class="card-body">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-fire class="w-10 h-10 text-error flex-shrink-0" />
                                <div>
                                    <h4 class="font-semibold">Lembur</h4>
                                    <p class="text-xs opacity-60 mt-1">Rekap jam lembur karyawan</p>
                                    <button class="btn btn-error btn-xs mt-3">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Bulanan -->
                    <div class="card bg-base-200 border border-base-300 hover:shadow-lg transition-all cursor-pointer">
                        <div class="card-body">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-document-chart-bar class="w-10 h-10 text-secondary flex-shrink-0" />
                                <div>
                                    <h4 class="font-semibold">Rekap Bulanan</h4>
                                    <p class="text-xs opacity-60 mt-1">Laporan komprehensif bulanan</p>
                                    <button class="btn btn-secondary btn-xs mt-3">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
