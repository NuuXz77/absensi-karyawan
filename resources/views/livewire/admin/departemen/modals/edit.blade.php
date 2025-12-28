<div>
    <dialog id="modal_edit_departemen" class="modal" wire:ignore.self>
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold">Edit Departemen</h3>
            
            <form wire:submit.prevent="update">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                    {{-- KODE DEPARTEMEN --}}
                    <fieldset>
                        <legend class="fieldset-legend">KODE DEPARTEMEN</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('kode_departemen') input-error @enderror">
                            <x-heroicon-o-hashtag class="w-4 h-4 opacity-70" />
                            <input required type="text" wire:model="kode_departemen" placeholder="Contoh: HR" class="uppercase" maxlength="50" />
                        </label>
                        <p class="validator-hint hidden">Kode departemen wajib diisi</p>
                    </fieldset>
                    
                    {{-- NAMA DEPARTEMEN --}}
                    <fieldset>
                        <legend class="fieldset-legend">NAMA DEPARTEMEN</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('nama_departemen') input-error @enderror">
                            <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                            <input required type="text" wire:model="nama_departemen" placeholder="Contoh: Human Resource" />
                        </label>
                        <p class="validator-hint hidden">Nama departemen wajib diisi</p>
                    </fieldset>
                    
                    {{-- STATUS DEPARTEMEN --}}
                    <fieldset>
                        <legend class="fieldset-legend">STATUS DEPARTEMEN</legend>
                        <label class="cursor-pointer label justify-start gap-3">
                            <input type="checkbox" 
                                wire:change="$set('status', $event.target.checked ? 'active' : 'inactive')" 
                                {{ $status === 'active' ? 'checked' : '' }} 
                                class="toggle toggle-success" />
                            <span class="label-text flex items-center gap-2">
                                <x-heroicon-o-check-circle class="w-5 h-5 text-success" />
                                Status Aktif
                            </span>
                        </label>
                    </fieldset>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary gap-2 btn-sm" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update" class="flex items-center gap-2">
                                <x-heroicon-o-check class="w-5 h-5" />
                                Update
                            </span>
                            <span wire:loading wire:target="update" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-sm"></span>
                                Mengupdate data...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </dialog>
    
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Departemen berhasil diupdate!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal mengupdate data!' }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-edit-modal', (event) => {
            document.getElementById('modal_edit_departemen').showModal();
        });
        
        Livewire.on('close-edit-modal', () => {
            document.getElementById('modal_edit_departemen').close();
        });
    });
</script>
