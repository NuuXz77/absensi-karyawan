<div>
    <dialog id="modal_edit_saldo" class="modal" wire:ignore.self>
        <div class="modal-box max-w-3xl border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold">Edit Saldo Cuti & Izin</h3>
            
            <form wire:submit.prevent="update">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                    {{-- KARYAWAN --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">KARYAWAN</legend>
                        <label class="w-full">
                            <select required wire:model="karyawan_id" class="select select-bordered w-full @error('karyawan_id') select-error @enderror">
                                <option value="">Pilih Karyawan</option>
                                @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}">
                                        {{ $karyawan->nip }} - {{ $karyawan->nama_lengkap }} ({{ $karyawan->jabatan->nama_jabatan ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        @error('karyawan_id')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- TAHUN --}}
                    <fieldset>
                        <legend class="fieldset-legend">TAHUN</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('tahun') input-error @enderror">
                            <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                            <input required type="number" wire:model="tahun" placeholder="2026" min="2020" max="2100" />
                        </label>
                        @error('tahun')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div class="col-span-2">
                        <div class="divider">SALDO IZIN</div>
                    </div>
                    
                    {{-- TOTAL IZIN --}}
                    <fieldset>
                        <legend class="fieldset-legend">TOTAL IZIN (HARI)</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('total_izin') input-error @enderror">
                            <x-heroicon-o-clipboard-document-check class="w-4 h-4 opacity-70" />
                            <input required type="number" wire:model="total_izin" placeholder="12" min="0" max="365" />
                        </label>
                        @error('total_izin')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- SISA IZIN --}}
                    <fieldset>
                        <legend class="fieldset-legend">SISA IZIN (HARI)</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('sisa_izin') input-error @enderror">
                            <x-heroicon-o-clipboard-document-check class="w-4 h-4 opacity-70" />
                            <input required type="number" wire:model="sisa_izin" placeholder="12" min="0" max="365" />
                        </label>
                        @error('sisa_izin')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div class="col-span-2">
                        <div class="divider">SALDO CUTI</div>
                    </div>
                    
                    {{-- TOTAL CUTI --}}
                    <fieldset>
                        <legend class="fieldset-legend">TOTAL CUTI (HARI)</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('total_cuti') input-error @enderror">
                            <x-heroicon-o-calendar-days class="w-4 h-4 opacity-70" />
                            <input required type="number" wire:model="total_cuti" placeholder="12" min="0" max="365" />
                        </label>
                        @error('total_cuti')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- SISA CUTI --}}
                    <fieldset>
                        <legend class="fieldset-legend">SISA CUTI (HARI)</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('sisa_cuti') input-error @enderror">
                            <x-heroicon-o-calendar-days class="w-4 h-4 opacity-70" />
                            <input required type="number" wire:model="sisa_cuti" placeholder="12" min="0" max="365" />
                        </label>
                        @error('sisa_cuti')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <!-- Form Actions -->
                    <div class="col-span-2 flex justify-end gap-3 mt-6">
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
            <div wire:key="success-edit-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Saldo berhasil diupdate!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-edit-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal mengupdate data!' }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-edit-modal', (event) => {
            document.getElementById('modal_edit_saldo').showModal();
        });
        
        Livewire.on('hide-edit-modal', () => {
            document.getElementById('modal_edit_saldo').close();
        });
    });
</script>
