<div>
    <dialog id="modal_delete_saldo" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            
            <div class="flex flex-col items-center gap-4 py-4">
                <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                    <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-error" />
                </div>
                
                <div class="text-center">
                    <h3 class="text-lg font-bold mb-2">Konfirmasi Hapus Saldo</h3>
                    <p class="text-sm text-base-content/70">Apakah Anda yakin ingin menghapus saldo ini?</p>
                </div>

                @if($saldoInfo)
                    <div class="w-full bg-base-200 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-base-content/70">NIP:</span>
                            <span class="font-semibold">{{ $saldoInfo['nip'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-base-content/70">Karyawan:</span>
                            <span class="font-semibold">{{ $saldoInfo['karyawan'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-base-content/70">Tahun:</span>
                            <span class="font-semibold">{{ $saldoInfo['tahun'] }}</span>
                        </div>
                    </div>
                @endif

                <div class="alert alert-warning">
                    <x-heroicon-o-exclamation-circle class="w-5 h-5" />
                    <span class="text-sm">Data yang dihapus tidak dapat dikembalikan!</span>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    Batal
                </button>
                <button type="button" wire:click="delete" class="btn btn-error gap-2 btn-sm" wire:loading.attr="disabled" wire:target="delete">
                    <span wire:loading.remove wire:target="delete" class="flex items-center gap-2">
                        <x-heroicon-o-trash class="w-5 h-5" />
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="delete" class="flex items-center gap-2">
                        <span class="loading loading-spinner loading-sm"></span>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </dialog>
    
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-delete-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Saldo berhasil dihapus!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-delete-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal menghapus data!' }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-delete-modal', (event) => {
            document.getElementById('modal_delete_saldo').showModal();
        });
        
        Livewire.on('hide-delete-modal', () => {
            document.getElementById('modal_delete_saldo').close();
        });
    });
</script>
