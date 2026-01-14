<dialog id="modal_informasi_sistem" class="modal" wire:ignore.self>
    <div class="modal-box max-w-2xl border border-base-300">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
        </form>
        
        <h3 class="text-lg font-bold mb-4 pb-4 border-b border-base-300 flex items-center gap-2">
            <x-heroicon-o-information-circle class="h-6 w-6 text-primary" />
            Informasi Sistem
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- VERSI APLIKASI --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">VERSI APLIKASI</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-code-bracket class="w-4 h-4 opacity-70" />
                    <input type="text" value="1.0.0" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- TANGGAL DIBUAT --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">TANGGAL DIBUAT</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $user->created_at->format('d M Y, H:i') }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- TERAKHIR DIUPDATE --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">TERAKHIR DIUPDATE</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-arrow-path class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $user->updated_at->format('d M Y, H:i') }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- ID USER --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">ID USER</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-key class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $user->id }}" class="grow" readonly />
                </label>
            </fieldset>
        </div>
        
        <div class="mt-4 p-4 bg-base-200 rounded-lg">
            <div class="flex items-center gap-3">
                <x-heroicon-o-server class="w-6 h-6 text-info" />
                <div>
                    <p class="font-semibold">Sistem Absensi Karyawan</p>
                    <p class="text-sm text-base-content/70">Platform terintegrasi untuk manajemen kehadiran</p>
                </div>
            </div>
        </div>
        
        {{-- Modal Footer --}}
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                <x-heroicon-o-x-mark class="w-5 h-5" />
                Tutup
            </button>
        </div>
    </div>
    <div class="modal-backdrop" wire:click="closeModal"></div>
</dialog>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-informasi-sistem-modal', () => {
            document.getElementById('modal_informasi_sistem').showModal();
        });
        
        Livewire.on('close-informasi-sistem-modal', () => {
            document.getElementById('modal_informasi_sistem').close();
        });
    });
</script>
