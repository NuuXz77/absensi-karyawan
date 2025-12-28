<div>
    <dialog id="modal_delete_karyawan" class="modal" wire:ignore.self>
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold flex items-center gap-2 text-error">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                Konfirmasi Hapus
            </h3>
            
            <div class="py-4">
                <p class="text-base mb-4">Apakah Anda yakin ingin menghapus karyawan ini?</p>
                
                @if($karyawan)
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="mask mask-squircle w-16 h-16">
                                    @if ($karyawan->foto_karyawan)
                                        <img src="{{ Storage::url($karyawan->foto_karyawan) }}" alt="{{ $karyawan->nama_lengkap }}" />
                                    @else
                                        <div class="bg-primary text-primary-content flex items-center justify-center w-full h-full">
                                            <span class="text-2xl font-bold">{{ substr($karyawan->nama_lengkap, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="font-bold">{{ $karyawan->nama_lengkap }}</div>
                                <div class="text-sm opacity-70">{{ $karyawan->id_card }}</div>
                            </div>
                        </div>
                        <div class="divider my-2"></div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-briefcase class="w-4 h-4 opacity-70" />
                                <span>{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                                <span>{{ $karyawan->departemen->nama_departemen ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                <span>{{ $karyawan->absensi_count ?? 0 }} Absensi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-document-text class="w-4 h-4 opacity-70" />
                                <span>{{ ($karyawan->izin_count ?? 0) + ($karyawan->cuti_count ?? 0) }} Izin/Cuti</span>
                            </div>
                        </div>
                    </div>
                    
                    @if(($karyawan->absensi_count ?? 0) > 0 || ($karyawan->izin_count ?? 0) > 0 || ($karyawan->cuti_count ?? 0) > 0)
                        <div class="alert alert-warning mt-4">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            <span>Karyawan ini memiliki riwayat absensi/izin/cuti. Data tidak dapat dihapus!</span>
                        </div>
                    @endif
                @endif
                
                <p class="text-sm opacity-70 mt-4">Tindakan ini akan menghapus akun login dan semua data terkait. Tidak dapat dibatalkan!</p>
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
                <span>Karyawan berhasil dihapus!</span>
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
            document.getElementById('modal_delete_karyawan').showModal();
        });
        
        Livewire.on('close-delete-modal', () => {
            document.getElementById('modal_delete_karyawan').close();
        });
    });
</script>
