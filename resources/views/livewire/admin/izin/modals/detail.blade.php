<div x-data="modal('modal_detail_izin')">
    <dialog id="modal_detail_izin" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300 max-w-4xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold flex items-center gap-2">
                <x-heroicon-o-document-text class="w-6 h-6" />
                Detail Pengajuan Izin
            </h3>
            
            <div class="divider"></div>
            
            <div class="space-y-4">
                {{-- Status Badge --}}
                <div class="flex justify-between items-center">
                    <span class="text-sm text-base-content/60">Status Pengajuan</span>
                    <span class="badge badge-lg 
                        {{ $status === 'pending' ? 'badge-warning' : '' }}
                        {{ $status === 'disetujui' ? 'badge-success' : '' }}
                        {{ $status === 'ditolak' ? 'badge-error' : '' }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                    <div class="divider"></div>

                    {{-- Informasi Karyawan --}}
                    <div class="p-4 rounded-lg">
                        <h4 class="font-semibold mb-3 flex items-center gap-2">
                            <x-heroicon-o-user class="w-5 h-5 text-primary" />
                            Informasi Karyawan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <fieldset>
                                <legend class="fieldset-legend">NAMA LENGKAP</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="nama_lengkap" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>

                            <fieldset>
                                <legend class="fieldset-legend">NIP</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-identification class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="nip" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>

                            <fieldset>
                                <legend class="fieldset-legend">DEPARTEMEN</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="nama_departemen" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>

                            <fieldset>
                                <legend class="fieldset-legend">JABATAN</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-briefcase class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="nama_jabatan" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>
                        </div>
                    </div>

                    {{-- Informasi Izin --}}
                    <div class="p-4 rounded-lg">
                        <h4 class="font-semibold mb-3 flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5 text-secondary" />
                            Informasi Izin
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <fieldset>
                                <legend class="fieldset-legend">TANGGAL MULAI</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="tanggal_mulai" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>

                            <fieldset>
                                <legend class="fieldset-legend">TANGGAL SELESAI</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="tanggal_selesai" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>

                            <fieldset>
                                <legend class="fieldset-legend">JENIS IZIN</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-document-text class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="jenis_izin" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>

                            <fieldset>
                                <legend class="fieldset-legend">DURASI</legend>
                                <label class="input w-full input-bordered flex items-center gap-2">
                                    <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                                    <input 
                                        type="text" 
                                        wire:model="durasi" 
                                        disabled 
                                    />
                                </label>
                            </fieldset>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="p-4 rounded-lg">
                        <h4 class="font-semibold mb-3 flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-info" />
                            Keterangan
                        </h4>
                        <fieldset>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-document-text class="w-4 h-4 opacity-70" />
                                <input 
                                    type="text" 
                                    wire:model="alasan" 
                                    disabled 
                                />
                            </label>
                        </fieldset>
                    </div>

                    {{-- Bukti Foto --}}
                    @if($bukti_foto)
                        <div class="p-4 rounded-lg">
                            <h4 class="font-semibold mb-3 flex items-center gap-2">
                                <x-heroicon-o-camera class="w-5 h-5 text-accent" />
                                Bukti Foto
                            </h4>
                            <div class="relative group">
                                <img src="{{ Storage::url($bukti_foto) }}" 
                                     alt="Bukti Foto" 
                                     class="rounded-lg border border-base-300 w-full max-h-96 object-contain bg-base-100">
                                <a href="{{ Storage::url($bukti_foto) }}" 
                                   target="_blank" 
                                   class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                    <x-heroicon-o-magnifying-glass-plus class="w-12 h-12 text-white" />
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Informasi Persetujuan --}}
                    @if($status !== 'pending')
                        <div class="p-4 rounded-lg">
                            <h4 class="font-semibold mb-3 flex items-center gap-2">
                                <x-heroicon-o-check-circle class="w-5 h-5 text-success" />
                                Informasi Persetujuan
                            </h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-sm text-base-content/60">Diproses Oleh</p>
                                    <p class="font-semibold">{{ $disetujui_oleh }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-base-content/60">Tanggal Diproses</p>
                                    <p class="font-semibold">{{ $tanggal_diproses }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Timestamp --}}
                    <div class="text-xs text-base-content/50 pt-4 border-t border-base-300">
                        <p>Diajukan pada: {{ $tanggal_diajukan }}</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 mt-6">
                    @if($status === 'pending')
                        <button type="button" wire:click="$dispatch('open-confirm-modal', {id: {{ $izinId }}, action: 'reject'})" class="btn btn-error btn-sm gap-2">
                            <x-heroicon-o-x-circle class="w-5 h-5" />
                            Tolak
                        </button>
                        <button type="button" wire:click="$dispatch('open-confirm-modal', {id: {{ $izinId }}, action: 'approve'})" class="btn btn-success btn-sm gap-2">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            Setujui
                        </button>
                    @endif
                </div>
        </div>
    </dialog>
</div>

{{-- <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-detail-modal', (event) => {
            document.getElementById('modal_detail_izin').showModal();
        });
        
        Livewire.on('close-detail-modal', () => {
            document.getElementById('modal_detail_izin').close();
        });
    });
</script> --}}
