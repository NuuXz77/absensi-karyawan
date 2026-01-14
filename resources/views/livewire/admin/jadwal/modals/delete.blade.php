<div>
    <dialog id="modal_delete_jadwal" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-error/10 p-3 rounded-full">
                    <x-heroicon-o-trash class="w-8 h-8 text-error" />
                </div>
                <div>
                    <h3 class="text-lg font-bold">Hapus Jadwal Kerja</h3>
                    <p class="text-sm opacity-70">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            @if($jadwalInfo)
                <div class="alert alert-warning mb-4">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    <div class="text-sm">
                        <p class="font-semibold">Apakah Anda yakin ingin menghapus jadwal ini?</p>
                        <div class="mt-2 space-y-1">
                            <p><strong>Karyawan:</strong> {{ $jadwalInfo['karyawan'] }}</p>
                            <p><strong>Departemen:</strong> {{ $jadwalInfo['departemen'] }}</p>
                            <p><strong>Tanggal:</strong> {{ $jadwalInfo['tanggal'] }}</p>
                            <p><strong>Shift:</strong> {{ $jadwalInfo['shift'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    Batal
                </button>
                <button type="button" wire:click="delete" class="btn btn-error btn-sm gap-2" wire:loading.attr="disabled" wire:target="delete">
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
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Jadwal berhasil dihapus!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-delete-modal', () => {
            document.getElementById('modal_delete_jadwal').showModal();
        });
        
        Livewire.on('close-delete-modal', () => {
            document.getElementById('modal_delete_jadwal').close();
        });
    });
</script>
