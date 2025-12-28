<div>
    <dialog id="modal_create_jadwal" class="modal" wire:ignore.self>
        <div class="modal-box max-w-2xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold">Tambah Jadwal Kerja</h3>
            
            <form wire:submit.prevent="save" class="mt-4">
                <div class="grid grid-cols-2 gap-4">
                    {{-- KARYAWAN --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">KARYAWAN</legend>
                        <select wire:model="karyawan_id" class="select select-bordered w-full @error('karyawan_id') select-error @enderror">
                            <option value="">Pilih Karyawan</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}">
                                    {{ $karyawan->nama_lengkap }} 
                                    @if($karyawan->departemen)
                                        - {{ $karyawan->departemen->nama_departemen }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- TANGGAL --}}
                    <fieldset>
                        <legend class="fieldset-legend">TANGGAL</legend>
                        <input type="date" wire:model="tanggal" class="input input-bordered w-full @error('tanggal') input-error @enderror" />
                        @error('tanggal')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- SHIFT --}}
                    <fieldset>
                        <legend class="fieldset-legend">SHIFT</legend>
                        <select wire:model="shift_id" class="select select-bordered w-full @error('shift_id') select-error @enderror">
                            <option value="">Pilih Shift</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->nama_shift }} ({{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- STATUS --}}
                    <fieldset>
                        <legend class="fieldset-legend">STATUS</legend>
                        <select wire:model="status" class="select select-bordered w-full">
                            <option value="aktif">Aktif</option>
                            <option value="libur">Libur</option>
                        </select>
                    </fieldset>

                    {{-- KETERANGAN --}}
                    <fieldset>
                        <legend class="fieldset-legend">KETERANGAN (Opsional)</legend>
                        <textarea wire:model="keterangan" class="textarea textarea-bordered w-full" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </fieldset>

                    {{-- ACTIONS --}}
                    <div class="col-span-2 flex justify-end gap-3 mt-4">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm gap-2" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                                <x-heroicon-o-check class="w-5 h-5" />
                                Simpan
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-sm"></span>
                                Menyimpan...
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
                <span>Jadwal berhasil ditambahkan!</span>
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
        Livewire.on('open-create-modal', () => {
            document.getElementById('modal_create_jadwal').showModal();
        });
        
        Livewire.on('close-create-modal', () => {
            document.getElementById('modal_create_jadwal').close();
        });
    });
</script>
