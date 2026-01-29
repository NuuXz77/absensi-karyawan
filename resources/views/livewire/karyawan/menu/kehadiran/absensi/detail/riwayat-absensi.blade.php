
<div class="space-y-6">
    <x-slot name="header">
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('karyawan.kehadiran.riwayat-absensi') }}" wire:navigate class="btn btn-circle btn-ghost">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-base-content">Detail Absensi</h1>
                <p class="text-sm text-base-content/60 mt-1">
                    {{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>
    </x-slot>
    {{-- Status Badge --}}
    <div class="card bg-base-100 shadow border border-base-300">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @php
                        $iconClass = match($absensi->status) {
                            'tepat_waktu' => 'text-success',
                            'terlambat' => 'text-warning',
                            'izin' => 'text-info',
                            'cuti' => 'text-secondary',
                            'alpha' => 'text-error',
                            default => 'text-base-content'
                        };
                        $badgeClass = match($absensi->status) {
                            'tepat_waktu' => 'badge-success',
                            'terlambat' => 'badge-warning',
                            'izin' => 'badge-info',
                            'cuti' => 'badge-secondary',
                            'alpha' => 'badge-error',
                            default => 'badge-ghost'
                        };
                        $statusText = match($absensi->status) {
                            'tepat_waktu' => 'Tepat Waktu',
                            'terlambat' => 'Terlambat',
                            'izin' => 'Izin',
                            'cuti' => 'Cuti',
                            'alpha' => 'Alpha',
                            default => ucfirst($absensi->status)
                        };
                    @endphp
                    <div class="avatar placeholder">
                        <div class="bg-base-200 {{ $iconClass }} rounded-full w-16 flex items-center justify-center">
                            @if($absensi->status == 'tepat_waktu')
                                <x-heroicon-o-check-circle class="w-8 h-8" />
                            @elseif($absensi->status == 'terlambat')
                                <x-heroicon-o-clock class="w-8 h-8" />
                            @elseif($absensi->status == 'izin')
                                <x-heroicon-o-document-text class="w-8 h-8" />
                            @elseif($absensi->status == 'cuti')
                                <x-heroicon-o-calendar class="w-8 h-8" />
                            @else
                                <x-heroicon-o-x-circle class="w-8 h-8" />
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Status Kehadiran</h3>
                        <span class="badge badge-soft {{ $badgeClass }} badge-lg mt-2">{{ $statusText }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Waktu --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Jam Masuk --}}
        <div class="card bg-base-100 shadow border border-base-300">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="avatar placeholder">
                        <div class="bg-success/10 text-success rounded-full w-12 flex items-center justify-center">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm text-base-content/60">Jam Masuk</h4>
                        @if($absensi->jam_masuk)
                            <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}</p>
                            <p class="text-xs text-base-content/40">{{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('d M Y') }}</p>
                        @else
                            <p class="text-2xl font-bold text-base-content/40">-</p>
                            <p class="text-xs text-base-content/40">Belum absen masuk</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Jam Pulang --}}
        <div class="card bg-base-100 shadow border border-base-300">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="avatar placeholder">
                        <div class="bg-error/10 text-error rounded-full w-12 flex items-center justify-center">
                            <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm text-base-content/60">Jam Pulang</h4>
                        @if($absensi->jam_pulang)
                            <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}</p>
                            <p class="text-xs text-base-content/40">{{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('d M Y') }}</p>
                        @else
                            <p class="text-2xl font-bold text-base-content/40">-</p>
                            <p class="text-xs text-base-content/40">Belum absen pulang</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Lokasi --}}
    <div class="card bg-base-100 shadow border border-base-300">
        <div class="card-body">
            <h3 class="card-title flex items-center gap-2">
                <x-heroicon-o-map-pin class="w-6 h-6 text-primary" />
                Lokasi Absensi
            </h3>
            <div class="divider my-2"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-base-content/60 mb-1">Nama Lokasi</p>
                    <p class="font-semibold">{{ $absensi->lokasi->nama_lokasi }}</p>
                </div>
                {{-- <div>
                    <p class="text-sm text-base-content/60 mb-1">Alamat</p>
                    <p class="font-semibold">{{ $absensi->lokasi->alamat ?? '-' }}</p>
                </div> --}}
            </div>
        </div>
    </div>

    {{-- Koordinat Lokasi --}}
    <div class="card bg-base-100 shadow border border-base-300">
        <div class="card-body">
            <h3 class="card-title flex items-center gap-2">
                <x-heroicon-o-globe-alt class="w-6 h-6 text-secondary" />
                Koordinat Lokasi
            </h3>
            <div class="divider my-2"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Koordinat Masuk --}}
                <div>
                    <h4 class="font-semibold mb-3 flex items-center gap-2">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 text-success" />
                        Koordinat Masuk
                    </h4>
                    @if($absensi->lat_masuk && $absensi->long_masuk)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-base-content/60 w-20">Latitude:</span>
                                <code class="bg-base-200 px-2 py-1 rounded text-sm">{{ $absensi->lat_masuk }}</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-base-content/60 w-20">Longitude:</span>
                                <code class="bg-base-200 px-2 py-1 rounded text-sm">{{ $absensi->long_masuk }}</code>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $absensi->lat_masuk }},{{ $absensi->long_masuk }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline btn-primary mt-2">
                                <x-heroicon-o-map class="w-4 h-4" />
                                Lihat di Maps
                            </a>
                        </div>
                    @else
                        <p class="text-base-content/40">Tidak ada koordinat</p>
                    @endif
                </div>

                {{-- Koordinat Keluar --}}
                <div>
                    <h4 class="font-semibold mb-3 flex items-center gap-2">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 text-error" />
                        Koordinat Keluar
                    </h4>
                    @if($absensi->lat_keluar && $absensi->long_keluar)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-base-content/60 w-20">Latitude:</span>
                                <code class="bg-base-200 px-2 py-1 rounded text-sm">{{ $absensi->lat_keluar }}</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-base-content/60 w-20">Longitude:</span>
                                <code class="bg-base-200 px-2 py-1 rounded text-sm">{{ $absensi->long_keluar }}</code>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $absensi->lat_keluar }},{{ $absensi->long_keluar }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline btn-primary mt-2">
                                <x-heroicon-o-map class="w-4 h-4" />
                                Lihat di Maps
                            </a>
                        </div>
                    @else
                        <p class="text-base-content/40">Tidak ada koordinat</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Foto Absensi --}}
    <div class="card bg-base-100 shadow border border-base-300">
        <div class="card-body">
            <h3 class="card-title flex items-center gap-2">
                <x-heroicon-o-camera class="w-6 h-6 text-info" />
                Foto Absensi
            </h3>
            <div class="divider my-2"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Foto Masuk --}}
                <div>
                    <h4 class="font-semibold mb-3 flex items-center gap-2">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 text-success" />
                        Foto Masuk
                    </h4>
                    @if($absensi->foto_masuk)
                        <div class="relative group">
                            <img src="{{ Storage::url($absensi->foto_masuk) }}" 
                                 alt="Foto Masuk" 
                                 class="rounded-lg border border-base-300 w-full object-cover aspect-square">
                            <a href="{{ Storage::url($absensi->foto_masuk) }}" 
                               target="_blank" 
                               class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                <x-heroicon-o-magnifying-glass-plus class="w-12 h-12 text-white" />
                            </a>
                        </div>
                    @else
                        <div class="border-2 border-dashed border-base-300 rounded-lg p-8 flex flex-col items-center justify-center aspect-square">
                            <x-heroicon-o-photo class="w-12 h-12 text-base-content/20 mb-2" />
                            <p class="text-base-content/40 text-sm">Tidak ada foto</p>
                        </div>
                    @endif
                </div>

                {{-- Foto Keluar --}}
                <div>
                    <h4 class="font-semibold mb-3 flex items-center gap-2">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 text-error" />
                        Foto Keluar
                    </h4>
                    @if($absensi->foto_keluar)
                        <div class="relative group">
                            <img src="{{ Storage::url($absensi->foto_keluar) }}" 
                                 alt="Foto Keluar" 
                                 class="rounded-lg border border-base-300 w-full object-cover aspect-square">
                            <a href="{{ Storage::url($absensi->foto_keluar) }}" 
                               target="_blank" 
                               class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                <x-heroicon-o-magnifying-glass-plus class="w-12 h-12 text-white" />
                            </a>
                        </div>
                    @else
                        <div class="border-2 border-dashed border-base-300 rounded-lg p-8 flex flex-col items-center justify-center aspect-square">
                            <x-heroicon-o-photo class="w-12 h-12 text-base-content/20 mb-2" />
                            <p class="text-base-content/40 text-sm">Tidak ada foto</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Tambahan --}}
    <div class="card bg-base-100 shadow border border-base-300">
        <div class="card-body">
            <h3 class="card-title flex items-center gap-2">
                <x-heroicon-o-information-circle class="w-6 h-6 text-accent" />
                Informasi Tambahan
            </h3>
            <div class="divider my-2"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-base-content/60 mb-1">Tanggal Dicatat</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($absensi->created_at)->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}</p>
                </div>
                <div>
                    <p class="text-sm text-base-content/60 mb-1">Terakhir Diperbarui</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($absensi->updated_at)->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex justify-end gap-2">
        <a href="{{ route('karyawan.kehadiran.riwayat-absensi') }}" wire:navigate class="btn btn-outline">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            Kembali
        </a>
    </div>
</div>
