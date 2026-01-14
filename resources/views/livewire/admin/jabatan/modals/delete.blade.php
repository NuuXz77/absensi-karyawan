<div>
    <dialog id="modal_delete_jabatan" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold flex items-center gap-2 text-error">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                Konfirmasi Hapus
            </h3>
            
            <div class="py-4">
                <p class="text-base mb-4">Apakah Anda yakin ingin menghapus jabatan ini?</p>
                
                @if($jabatan)
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-briefcase class="w-5 h-5 opacity-70" />
                            <span class="font-semibold">{{ $jabatan->nama_jabatan }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-hashtag class="w-5 h-5 opacity-70" />
                            <span class="badge badge-ghost font-mono">{{ $jabatan->kode_jabatan }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-building-office class="w-5 h-5 opacity-70" />
                            <span>{{ $jabatan->departemen->nama_departemen ?? 'Semua Departemen' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-users class="w-5 h-5 opacity-70" />
                            <span>{{ $jabatan->karyawans_count ?? 0 }} Karyawan</span>
                        </div>
                    </div>
                    
                    @if($jabatan->karyawans_count > 0)
                        <div class="alert alert-warning mt-4">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            <span>Jabatan ini memiliki {{ $jabatan->karyawans_count }} karyawan. Pastikan untuk memindahkan karyawan terlebih dahulu!</span>
                        </div>
                    @endif
                @endif
                
                <p class="text-sm opacity-70 mt-4">Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    Batal
                </button>
                <button type="button" wire:click="delete" class="btn btn-error gap-2 btn-sm" wire:loading.attr="disabled" wire:target="delete">
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
        </div>
    </dialog>
    
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Jabatan berhasil dihapus!</span>
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
        Livewire.on('confirm-delete', (event) => {
            document.getElementById('modal_delete_jabatan').showModal();
        });
        
        Livewire.on('close-delete-modal', () => {
            document.getElementById('modal_delete_jabatan').close();
        });
    });
</script>
