<div x-data="modal('modal_create_jabatan')">
    <button class="btn btn-primary btn-sm gap-2"
        @click="openModal()">
        <x-heroicon-o-plus class="w-5 h-5" />
        <span class="hidden sm:inline">Tambah Jabatan</span>
    </button>
    @teleport('body')
        <dialog id="modal_create_jabatan" class="modal" wire:ignore.self x-show="open">
            <div class="modal-box max-w-2xl border border-base-300">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
            <h3 class="text-lg font-bold">Tambah Jabatan</h3>
            
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                    {{-- KODE JABATAN --}}
                    <fieldset>
                        <legend class="fieldset-legend">KODE JABATAN</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('kode_jabatan') input-error @enderror">
                            <x-heroicon-o-hashtag class="w-4 h-4 opacity-70" />
                            <input required type="text" wire:model="kode_jabatan" placeholder="Contoh: MGR" class="uppercase" maxlength="50" />
                        </label>
                        <p class="validator-hint hidden">Kode jabatan wajib diisi</p>
                    </fieldset>
                    
                    {{-- NAMA JABATAN --}}
                    <fieldset>
                        <legend class="fieldset-legend">NAMA JABATAN</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('nama_jabatan') input-error @enderror">
                            <x-heroicon-o-briefcase class="w-4 h-4 opacity-70" />
                            <input required type="text" wire:model="nama_jabatan" placeholder="Contoh: Manager HRD" />
                        </label>
                        <p class="validator-hint hidden">Nama jabatan wajib diisi</p>
                    </fieldset>
                    
                    {{-- DEPARTEMEN --}}
                    <fieldset>
                        <legend class="fieldset-legend">DEPARTEMEN</legend>
                        <label class="w-full">
                            <select wire:model="departemen_id" class="select select-bordered w-full @error('departemen_id') select-error @enderror">
                                <option value="">Pilih Departemen</option>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </label>
                        <p class="validator-hint hidden">Departemen wajib dipilih</p>
                    </fieldset>
                    
                    {{-- STATUS JABATAN --}}
                    <fieldset>
                        <legend class="fieldset-legend">STATUS JABATAN</legend>
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
    @endteleport
    
    <x-partials.toast 
        :success="$showSuccess ? 'Jabatan berhasil ditambahkan!' : null" 
        :error="$showError ? ($errorMessage ?: 'Gagal menyimpan data!') : null" 
    />
</div>