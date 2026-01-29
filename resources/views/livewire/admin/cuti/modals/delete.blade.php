<div>
    <dialog id="modal_delete_cuti" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold flex items-center gap-2">
                <x-heroicon-o-trash class="w-6 h-6 text-error" />
                Hapus Pengajuan Cuti
            </h3>
            
            @if($this->cuti)
                <div class="divider"></div>
                
                <div class="alert alert-warning">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    <div>
                        <h4 class="font-bold">Perhatian!</h4>
                        <p class="text-sm">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <p class="text-base-content/80">Apakah Anda yakin ingin menghapus pengajuan cuti ini?</p>
                    
                    {{-- Karyawan --}}
                    <fieldset>
                        <legend class="fieldset-legend">KARYAWAN</legend>
                        <label class="input w-full input-bordered flex items-center gap-2">
                            <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                            <input type="text" value="{{ $this->cuti->karyawan->nama_lengkap ?? '-' }}" disabled class="bg-base-200" />
                        </label>
                    </fieldset>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- Tanggal Mulai --}}
                        <fieldset>
                            <legend class="fieldset-legend">TANGGAL MULAI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ \Carbon\Carbon::parse($this->cuti->tanggal_mulai)->format('d/m/Y') }}" disabled class="bg-base-200" />
                            </label>
                        </fieldset>

                        {{-- Tanggal Selesai --}}
                        <fieldset>
                            <legend class="fieldset-legend">TANGGAL SELESAI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ \Carbon\Carbon::parse($this->cuti->tanggal_selesai)->format('d/m/Y') }}" disabled class="bg-base-200" />
                            </label>
                        </fieldset>

                        {{-- Durasi --}}
                        <fieldset>
                            <legend class="fieldset-legend">DURASI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ $this->cuti->jumlah_hari }} hari" disabled class="bg-base-200" />
                            </label>
                        </fieldset>

                        {{-- Status --}}
                        <fieldset>
                            <legend class="fieldset-legend">STATUS</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-information-circle class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ ucfirst($this->cuti->status) }}" disabled class="bg-base-200" />
                            </label>
                        </fieldset>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                        Batal
                    </button>
                    <button type="button" wire:click="delete" class="btn btn-error btn-sm gap-2" wire:loading.attr="disabled" wire:target="delete">
                        <span wire:loading.remove wire:target="delete" class="flex items-center gap-2">
                            <x-heroicon-o-trash class="w-5 h-5" />
                            Hapus
                        </span>
                        <span wire:loading wire:target="delete" class="flex items-center gap-2">
                            <span class="loading loading-spinner loading-sm"></span>
                            Menghapus...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </dialog>
    
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Data cuti berhasil dihapus{{ $this->cuti && $this->cuti->status === 'disetujui' ? ' dan saldo telah dikembalikan!' : '!' }}</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal menghapus data!' }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-delete-modal', (event) => {
            document.getElementById('modal_delete_cuti').showModal();
        });
        
        Livewire.on('close-delete-modal', () => {
            document.getElementById('modal_delete_cuti').close();
        });
    });
</script>
