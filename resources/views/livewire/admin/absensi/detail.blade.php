<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Informasi Karyawan -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">
                    <x-heroicon-o-user class="w-5 h-5" />
                    Informasi Karyawan
                </h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs opacity-60">NIP</label>
                        <p class="font-semibold">{{ $absensi->karyawan->nip ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs opacity-60">Nama Lengkap</label>
                        <p class="font-semibold">{{ $absensi->karyawan->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs opacity-60">Departemen</label>
                        <p class="font-semibold">{{ $absensi->karyawan->departemen->nama_departemen ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs opacity-60">Jabatan</label>
                        <p class="font-semibold">{{ $absensi->karyawan->jabatan->nama_jabatan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Waktu -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    Informasi Waktu
                </h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs opacity-60">Tanggal</label>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}</p>
                    </div>
                    <div>
                        <label class="text-xs opacity-60">Jam Masuk</label>
                        <p class="font-semibold">
                            @if($absensi->jam_masuk)
                                <span class="badge badge-success">{{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i:s') }}</span>
                            @else
                                <span class="badge badge-ghost">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs opacity-60">Jam Pulang</label>
                        <p class="font-semibold">
                            @if($absensi->jam_pulang)
                                <span class="badge badge-error">{{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i:s') }}</span>
                            @else
                                <span class="badge badge-ghost">Belum Pulang</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs opacity-60">Status</label>
                        <p class="font-semibold">
                            <span class="badge badge-sm 
                                {{ $absensi->status === 'hadir' ? 'badge-success' : '' }}
                                {{ $absensi->status === 'tepat_waktu' ? 'badge-success' : '' }}
                                {{ $absensi->status === 'terlambat' ? 'badge-warning' : '' }}
                                {{ $absensi->status === 'izin' ? 'badge-info' : '' }}
                                {{ $absensi->status === 'cuti' ? 'badge-info' : '' }}
                                {{ $absensi->status === 'alpha' ? 'badge-error' : '' }}">
                                @if($absensi->status === 'hadir')
                                    Hadir
                                @elseif($absensi->status === 'tepat_waktu')
                                    Tepat Waktu
                                @elseif($absensi->status === 'terlambat')
                                    Terlambat
                                @elseif($absensi->status === 'izin')
                                    Izin
                                @elseif($absensi->status === 'cuti')
                                    Cuti
                                @elseif($absensi->status === 'alpha')
                                    Alpha
                                @else
                                    -
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Lokasi -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">
                    <x-heroicon-o-map-pin class="w-5 h-5" />
                    Informasi Lokasi
                </h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs opacity-60">Lokasi</label>
                        <p class="font-semibold">{{ $absensi->lokasi->nama_lokasi ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs opacity-60">Alamat</label>
                        <p class="text-sm">{{ $absensi->lokasi->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Koordinat & Foto -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Koordinat Masuk -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                    Data Masuk
                </h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs opacity-60">Latitude</label>
                            <p class="font-mono text-sm">{{ $absensi->lat_masuk ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs opacity-60">Longitude</label>
                            <p class="font-mono text-sm">{{ $absensi->long_masuk ?? '-' }}</p>
                        </div>
                    </div>
                    @if($absensi->foto_masuk)
                        <div>
                            <label class="text-xs opacity-60 mb-2 block">Foto Masuk</label>
                            <div class="w-full aspect-square rounded-lg overflow-hidden border border-base-300">
                                <img src="{{ asset('storage/' . $absensi->foto_masuk) }}" 
                                     alt="Foto Masuk" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <x-heroicon-o-information-circle class="w-5 h-5" />
                            <span>Tidak ada foto masuk</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Koordinat Keluar -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">
                    <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5" />
                    Data Keluar
                </h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs opacity-60">Latitude</label>
                            <p class="font-mono text-sm">{{ $absensi->lat_keluar ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs opacity-60">Longitude</label>
                            <p class="font-mono text-sm">{{ $absensi->long_keluar ?? '-' }}</p>
                        </div>
                    </div>
                    @if($absensi->foto_keluar)
                        <div>
                            <label class="text-xs opacity-60 mb-2 block">Foto Keluar</label>
                            <div class="w-full aspect-square rounded-lg overflow-hidden border border-base-300">
                                <img src="{{ asset('storage/' . $absensi->foto_keluar) }}" 
                                     alt="Foto Keluar" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <x-heroicon-o-information-circle class="w-5 h-5" />
                            <span>Belum ada foto keluar</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('admin.absensi.index') }}" wire:navigate class="btn btn-ghost btn-sm">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            Kembali
        </a>
        <button wire:click="$dispatch('open-delete-modal', { absensiId: {{ $absensi->id }} })" class="btn btn-error btn-sm">
            <x-heroicon-o-trash class="w-5 h-5" />
            Hapus Absensi
        </button>
    </div>

    <!-- Delete Modal -->
    <livewire:admin.absensi.modals.delete />
</div>
