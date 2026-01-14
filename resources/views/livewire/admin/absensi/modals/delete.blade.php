<div>
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-error" />
                    Konfirmasi Hapus Data Absensi
                </h3>
                
                @if($absensi)
                    <div class="py-4">
                        <p class="text-base-content/70 mb-4">
                            Apakah Anda yakin ingin menghapus data absensi berikut?
                        </p>
                        
                        <div class="bg-base-200 p-4 rounded-lg space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-base-content/60">Karyawan:</span>
                                <span class="font-semibold">{{ $absensi->karyawan->nama_lengkap ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-base-content/60">Tanggal:</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('DD MMMM YYYY') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-base-content/60">Jam Masuk:</span>
                                <span class="font-semibold">{{ $absensi->jam_masuk ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-base-content/60">Jam Pulang:</span>
                                <span class="font-semibold">{{ $absensi->jam_pulang ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-base-content/60">Lokasi:</span>
                                <span class="font-semibold">{{ $absensi->lokasi->nama_lokasi ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-base-content/60">Status:</span>
                                <span class="badge badge-sm 
                                    {{ $absensi->status === 'tepat_waktu' ? 'badge-success' : '' }}
                                    {{ $absensi->status === 'terlambat' ? 'badge-warning' : '' }}
                                    {{ $absensi->status === 'alpha' ? 'badge-error' : '' }}">
                                    {{ ucfirst($absensi->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-4">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            <span class="text-sm">Data yang dihapus tidak dapat dikembalikan!</span>
                        </div>
                    </div>
                @endif
                
                <div class="modal-action">
                    <button wire:click="closeModal" class="btn btn-ghost">
                        Batal
                    </button>
                    <button wire:click="delete" class="btn btn-error" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="delete">
                            <x-heroicon-o-trash class="w-5 h-5" />
                            Hapus
                        </span>
                        <span wire:loading wire:target="delete" class="loading loading-spinner"></span>
                    </button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
