<div>
    <dialog id="modal_bulk_delete_jadwal" class="modal" wire:ignore.self>
        <div class="modal-box max-w-2xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-error/10 p-3 rounded-full">
                    <x-heroicon-o-trash class="w-8 h-8 text-error" />
                </div>
                <div>
                    <h3 class="text-lg font-bold">Hapus Jadwal Massal</h3>
                    <p class="text-sm opacity-70">Hapus banyak jadwal sekaligus berdasarkan filter</p>
                </div>
            </div>

            <form wire:submit.prevent="delete" class="mt-4">
                <div class="grid grid-cols-2 gap-4">
                    {{-- PERIODE --}}
                    <fieldset>
                        <legend class="fieldset-legend">TANGGAL MULAI</legend>
                        <input type="date" wire:model.live="tanggal_mulai" class="input input-bordered w-full @error('tanggal_mulai') input-error @enderror" />
                        @error('tanggal_mulai')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <legend class="fieldset-legend">TANGGAL SELESAI</legend>
                        <input type="date" wire:model.live="tanggal_selesai" class="input input-bordered w-full @error('tanggal_selesai') input-error @enderror" />
                        @error('tanggal_selesai')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- DEPARTEMEN (OPTIONAL) --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">DEPARTEMEN (Opsional - Kosongkan untuk semua)</legend>
                        <select wire:model.live="departemen_id" class="select select-bordered w-full">
                            <option value="">Semua Departemen</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </fieldset>

                    {{-- PREVIEW --}}
                    @if($tanggal_mulai && $tanggal_selesai)
                        <fieldset class="col-span-2">
                            <div class="alert alert-warning">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                                <div class="text-sm">
                                    <p class="font-semibold">Preview Penghapusan:</p>
                                    <p>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</p>
                                    @if($departemen_id)
                                        <p>Departemen: {{ $departemens->firstWhere('id', $departemen_id)->nama_departemen ?? '-' }}</p>
                                    @else
                                        <p>Departemen: Semua</p>
                                    @endif
                                    <p class="text-error font-bold mt-2">Total {{ $previewCount }} jadwal akan dihapus!</p>
                                </div>
                            </div>
                        </fieldset>
                    @endif

                    {{-- CONFIRMATION --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">KONFIRMASI (Ketik "HAPUS" untuk melanjutkan)</legend>
                        <input type="text" wire:model="confirmText" 
                               class="input input-bordered w-full @error('confirmText') input-error @enderror" 
                               placeholder="Ketik HAPUS untuk konfirmasi"
                               autocomplete="off" />
                        @error('confirmText')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- ACTIONS --}}
                    <div class="col-span-2 flex justify-end gap-3 mt-4">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                            Batal
                        </button>
                        <button type="submit" class="btn btn-error btn-sm gap-2" 
                                wire:loading.attr="disabled" 
                                wire:target="delete"
                                @disabled($previewCount === 0 || strtoupper($confirmText) !== 'HAPUS')>
                            <span wire:loading.remove wire:target="delete" class="flex items-center gap-2">
                                <x-heroicon-o-trash class="w-5 h-5" />
                                Hapus {{ $previewCount }} Jadwal
                            </span>
                            <span wire:loading wire:target="delete" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-sm"></span>
                                Menghapus...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Toast Notifications --}}
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>{{ $successMessage }}</span>
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
        Livewire.on('open-bulk-delete-modal-dialog', () => {
            document.getElementById('modal_bulk_delete_jadwal').showModal();
        });
        
        Livewire.on('close-bulk-delete-modal', () => {
            document.getElementById('modal_bulk_delete_jadwal').close();
        });
    });
</script>
