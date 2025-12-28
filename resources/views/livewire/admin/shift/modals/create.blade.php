<div>
    <button class="btn btn-primary btn-sm gap-2" wire:click="openModal">
        <x-heroicon-o-plus class="w-5 h-5" />
        <span class="hidden sm:inline">Tambah Shift</span>
    </button>
    <dialog id="modal_create_shift" class="modal" wire:ignore.self>
        <div class="modal-box max-w-2xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold">Tambah Shift</h3>
            
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                    {{-- NAMA SHIFT --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">NAMA SHIFT</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('nama_shift') input-error @enderror">
                            <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                            <input required type="text" wire:model="nama_shift" placeholder="Contoh: Shift Pagi" maxlength="255" />
                        </label>
                        @error('nama_shift')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- JAM MASUK --}}
                    <fieldset>
                        <legend class="fieldset-legend">JAM MASUK</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('jam_masuk') input-error @enderror">
                            <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                            <input required type="time" wire:model="jam_masuk" />
                        </label>
                        @error('jam_masuk')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- JAM PULANG --}}
                    <fieldset>
                        <legend class="fieldset-legend">JAM PULANG</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('jam_pulang') input-error @enderror">
                            <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                            <input required type="time" wire:model="jam_pulang" />
                        </label>
                        @error('jam_pulang')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- TOLERANSI KETERLAMBATAN --}}
                    <fieldset>
                        <legend class="fieldset-legend">TOLERANSI KETERLAMBATAN (MENIT)</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('toleransi_menit') input-error @enderror">
                            <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                            <input required type="number" wire:model="toleransi_menit" min="0" max="60" placeholder="15" />
                        </label>
                        @error('toleransi_menit')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- STATUS SHIFT --}}
                    <fieldset>
                        <legend class="fieldset-legend">STATUS SHIFT</legend>
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
                    <div class="col-span-2 flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary gap-2 btn-sm" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                                <x-heroicon-o-check class="w-5 h-5" />
                                Simpan
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-sm"></span>
                                Menyimpan data...
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
                <span>Shift berhasil ditambahkan!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal menyimpan data!' }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-create-modal', () => {
            document.getElementById('modal_create_shift').showModal();
        });
        
        Livewire.on('close-create-modal', () => {
            document.getElementById('modal_create_shift').close();
        });
    });
</script>
