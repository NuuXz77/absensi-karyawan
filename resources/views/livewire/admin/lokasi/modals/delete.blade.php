<div x-data="modal('modal_delete_lokasi')">
    @teleport('body')
        <dialog id="modal_delete_lokasi" class="modal" wire:ignore.self>
            <div class="modal-box border border-base-300">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
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
                    <button type="button" class="btn btn-error" wire:click="delete" wire:loading.attr="disabled">
                        <span wire:loading.remove>Hapus</span>
                        <span wire:loading>Menghapus...</span>
                    </button>
                </div>
            </div>
        </dialog>
    @endteleport

    <x-partials.toast :success="$showSuccess ? 'Lokasi berhasil dihapus!' : null" :error="$showError ? ($errorMessage ?: 'Gagal menghapus data!') : null" />
</div>
