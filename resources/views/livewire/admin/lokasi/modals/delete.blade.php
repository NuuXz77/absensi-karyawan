<div>
    <dialog id="modal_delete_lokasi" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="modal_delete_lokasi.close()">✕</button>
            </form>
            <h3 class="text-lg font-bold text-error">Hapus Lokasi</h3>
            <div class="py-4">
                <div class="alert alert-warning">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    <div>
                        <p class="font-semibold">Peringatan!</p>
                        <p class="text-sm">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <p class="mt-4">Apakah Anda yakin ingin menghapus lokasi <strong>{{ $nama_lokasi }}</strong>?</p>
            </div>
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="modal_delete_lokasi.close()">Batal</button>
                <button type="button" class="btn btn-error" wire:click="delete" wire:loading.attr="disabled">
                    <span wire:loading.remove>Hapus</span>
                    <span wire:loading>Menghapus...</span>
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button onclick="modal_delete_lokasi.close()">close</button>
        </form>
    </dialog>
    
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Lokasi berhasil dihapus!</span>
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
        Livewire.on('openDeleteModal', () => {
            modal_delete_lokasi.showModal();
        });
        
        Livewire.on('closeDeleteModal', () => {
            modal_delete_lokasi.close();
        });
    });
</script>
