<dialog id="modal_foto_identitas" class="modal" wire:ignore.self>
    <div class="modal-box max-w-2xl border border-base-300">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
        </form>
        
        <h3 class="text-lg font-bold mb-4 pb-4 border-b border-base-300 flex items-center gap-2">
            <x-heroicon-o-camera class="h-6 w-6 text-primary" />
            Foto & Identitas
        </h3>
        
        <div class="space-y-4">
            {{-- ID CARD --}}
            <fieldset>
                <legend class="fieldset-legend">ID CARD</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-identification class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->id_card }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- FOTO PROFIL --}}
            <fieldset>
                <legend class="fieldset-legend">FOTO PROFIL</legend>
                <div class="flex justify-center p-6 bg-base-200 rounded-lg">
                    <div class="avatar">
                        <div class="w-64 rounded-lg ring ring-primary ring-offset-base-100 ring-offset-2">
                            @if ($karyawan->foto_karyawan)
                                <img src="{{ asset('storage/' . $karyawan->foto_karyawan) }}" alt="Foto Karyawan" class="object-cover" />
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama_lengkap) }}&background=random&size=256" alt="Foto Profile" class="object-cover" />
                            @endif
                        </div>
                    </div>
                </div>
            </fieldset>
            
            {{-- INFO --}}
            <div class="alert alert-warning">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                <div class="flex flex-col">
                    <span class="font-semibold">Perhatian</span>
                    <span class="text-sm">Untuk mengubah foto profil, silakan hubungi administrator sistem.</span>
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
        Livewire.on('open-foto-identitas-modal', () => {
            document.getElementById('modal_foto_identitas').showModal();
        });
        
        Livewire.on('close-foto-identitas-modal', () => {
            document.getElementById('modal_foto_identitas').close();
        });
    });
</script>
