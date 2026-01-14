<dialog id="modal_data_pribadi" class="modal" wire:ignore.self>
    <div class="modal-box max-w-3xl border border-base-300">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
        </form>
        
        <h3 class="text-lg font-bold mb-4 pb-4 border-b border-base-300 flex items-center gap-2">
            <x-heroicon-o-document-text class="h-6 w-6 text-primary" />
            Data Pribadi
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- NAMA LENGKAP --}}
            <fieldset class="col-span-2">
                <legend class="fieldset-legend">NAMA LENGKAP</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->nama_lengkap }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- NIP --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">NIP</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-hashtag class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->nip }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- EMAIL --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">EMAIL</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-envelope class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->email }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- NO. TELEPON --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">NO. TELEPON</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-phone class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->no_telepon }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- JENIS KELAMIN --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">JENIS KELAMIN</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- TANGGAL LAHIR --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">TANGGAL LAHIR</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-cake class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') : '-' }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- JABATAN --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">JABATAN</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-briefcase class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->jabatan->nama_jabatan }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- ALAMAT --}}
            <fieldset class="col-span-2">
                <legend class="fieldset-legend">ALAMAT</legend>
                <label class="input w-full input-bordered flex items-start gap-2 h-auto">
                    <x-heroicon-o-map-pin class="w-4 h-4 opacity-70 mt-3" />
                    <textarea class="textarea w-full border-0 focus:outline-none p-0 resize-none" rows="3" readonly>{{ $karyawan->alamat }}</textarea>
                </label>
            </fieldset>
            
            {{-- DEPARTEMEN --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">DEPARTEMEN</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $karyawan->departemen->nama_departemen }}" class="grow" readonly />
                </label>
            </fieldset>
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
        Livewire.on('open-data-pribadi-modal', () => {
            document.getElementById('modal_data_pribadi').showModal();
        });
        
        Livewire.on('close-data-pribadi-modal', () => {
            document.getElementById('modal_data_pribadi').close();
        });
    });
</script>
