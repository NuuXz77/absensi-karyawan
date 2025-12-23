<div>
    <!-- Modal -->
    <input type="checkbox" id="modal-create-karyawan" class="modal-toggle" {{ $showModal ? 'checked' : '' }} />
    <div class="modal modal-bottom sm:modal-middle {{ $showModal ? 'modal-open' : '' }}" role="dialog">
        <div class="modal-box max-w-5xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6 sticky top-0 bg-base-100 z-10 pb-4">
                <div>
                    <h3 class="text-2xl font-bold flex items-center gap-2">
                        <x-heroicon-o-user-plus class="w-7 h-7 text-primary" />
                        Tambah Karyawan Baru
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Lengkapi semua data karyawan dengan teliti</p>
                </div>
                <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Foto Karyawan -->
                        <div class="card bg-base-200 shadow-sm">
                            <div class="card-body p-4">
                                <div class="flex flex-col items-center gap-3">
                                    <!-- Preview -->
                                    <div class="avatar">
                                        <div class="w-32 h-32 rounded-xl ring ring-primary ring-offset-base-100 ring-offset-2">
                                            @if ($foto_karyawan)
                                                <img src="{{ $foto_karyawan->temporaryUrl() }}" alt="Preview" />
                                            @else
                                                <div class="bg-base-300 flex items-center justify-center w-full h-full">
                                                    <x-heroicon-o-photo class="w-12 h-12 text-gray-400" />
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Upload Button -->
                                    <label class="btn btn-outline btn-sm gap-2 cursor-pointer">
                                        <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                                        {{ $foto_karyawan ? 'Ganti Foto' : 'Upload Foto' }}
                                        <input type="file" wire:model="foto_karyawan" accept="image/*" class="hidden" />
                                    </label>
                                    
                                    @if($foto_karyawan)
                                        <button type="button" wire:click="removeFoto" class="btn btn-ghost btn-xs text-error gap-1">
                                            <x-heroicon-o-trash class="w-3 h-3" />
                                            Hapus Foto
                                        </button>
                                    @endif

                                    <div wire:loading wire:target="foto_karyawan" class="text-center">
                                        <span class="loading loading-spinner loading-sm"></span>
                                        <span class="text-xs ml-2">Mengupload foto...</span>
                                    </div>
                                </div>
                                @error('foto_karyawan')
                                    <div class="alert alert-error alert-sm mt-2">
                                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Data Pribadi Card -->
                        <div class="card bg-base-100 shadow-sm border">
                            <div class="card-body p-4">
                                <h4 class="card-title text-base flex items-center gap-2 mb-4">
                                    <x-heroicon-o-identification class="w-5 h-5 text-primary" />
                                    Data Pribadi
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-4">
                                    <!-- NIP -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('nip') input-error @enderror">
                                            <x-heroicon-o-hashtag class="w-4 h-4 opacity-70" />
                                            <input type="text" wire:model="nip" placeholder="NIP" class="w-full" />
                                        </label>
                                        @error('nip')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>

                                    <!-- NIK / ID Card -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('id_card') input-error @enderror">
                                            <x-heroicon-o-identification class="w-4 h-4 opacity-70" />
                                            <input type="text" wire:model="id_card" placeholder="NIK (16 digit)" maxlength="16" class="w-full" />
                                        </label>
                                        @error('id_card')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>

                                    <!-- Nama Lengkap -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('nama_lengkap') input-error @enderror">
                                            <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                                            <input type="text" wire:model="nama_lengkap" placeholder="Nama Lengkap" class="w-full" />
                                        </label>
                                        @error('nama_lengkap')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('tanggal_lahir') input-error @enderror">
                                            <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                            <input type="date" wire:model="tanggal_lahir" class="w-full" />
                                        </label>
                                        @error('tanggal_lahir')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Data Akun & Pekerjaan Card -->
                        <div class="card bg-base-100 shadow-sm border">
                            <div class="card-body p-4">
                                <h4 class="card-title text-base flex items-center gap-2 mb-4">
                                    <x-heroicon-o-briefcase class="w-5 h-5 text-primary" />
                                    Data Akun & Pekerjaan
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Email -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('email') input-error @enderror">
                                            <x-heroicon-o-envelope class="w-4 h-4 opacity-70" />
                                            <input type="email" wire:model="email" placeholder="Email" class="w-full" />
                                        </label>
                                        @error('email')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>

                                    <!-- Username -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('username') input-error @enderror">
                                            <x-heroicon-o-at-symbol class="w-4 h-4 opacity-70" />
                                            <input type="text" wire:model="username" placeholder="Username" class="w-full" />
                                        </label>
                                        @error('username')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>

                                    <!-- Password (Auto Generated) -->
                                    <div class="form-control">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium flex items-center gap-2">
                                                <x-heroicon-o-key class="w-4 h-4" />
                                                Password (Otomatis)
                                            </span>
                                            <button type="button" wire:click="generatePassword" class="btn btn-ghost btn-xs gap-1">
                                                <x-heroicon-o-arrow-path class="w-3 h-3" />
                                                Generate
                                            </button>
                                        </div>
                                        <div class="flex gap-2">
                                            <label class="input input-bordered flex items-center gap-2 bg-base-200 flex-1">
                                                <x-heroicon-o-lock-closed class="w-4 h-4 opacity-70" />
                                                <input type="text" value="{{ $generatedPassword }}" readonly class="grow font-mono" />
                                            </label>
                                            <button type="button" wire:click="copyPassword" class="btn btn-ghost" title="Copy password">
                                                <x-heroicon-o-clipboard class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <div class="alert alert-warning alert-sm mt-2">
                                            <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                                            <span class="text-xs">Password untuk login pertama kali</span>
                                        </div>
                                    </div>

                                    <!-- Jabatan -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('jabatan') input-error @enderror">
                                            <x-heroicon-o-identification class="w-4 h-4 opacity-70" />
                                            <input type="text" wire:model="jabatan" placeholder="Jabatan" class="w-full" />
                                        </label>
                                        @error('jabatan')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>

                                    <!-- Departemen -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('departemen') input-error @enderror">
                                            <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                                            <select wire:model="departemen" class="grow bg-transparent">
                                                <option value="">Pilih Departemen</option>
                                                <option value="IT">IT</option>
                                                <option value="HR">HR</option>
                                                <option value="Finance">Finance</option>
                                                <option value="Marketing">Marketing</option>
                                                <option value="Operations">Operations</option>
                                            </select>
                                        </label>
                                        @error('departemen')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="form-control">
                                        <label class="input input-bordered flex items-center gap-2 @error('status') input-error @enderror">
                                            <x-heroicon-o-check-badge class="w-4 h-4 opacity-70" />
                                            <select wire:model="status" class="grow bg-transparent">
                                                <option value="aktif">Aktif</option>
                                                <option value="nonaktif">Non-Aktif</option>
                                                <option value="cuti">Cuti</option>
                                            </select>
                                        </label>
                                        @error('status')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="card bg-info/10 border-info/20 mt-6">
                    <div class="card-body p-4">
                        <div class="flex items-start gap-3">
                            <x-heroicon-o-light-bulb class="w-6 h-6 text-info flex-shrink-0 mt-1" />
                            <div class="text-sm space-y-2">
                                <p class="font-bold text-base">📋 Informasi Penting:</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="flex items-start gap-2">
                                        <x-heroicon-o-key class="w-4 h-4 mt-0.5 flex-shrink-0 text-info" />
                                        <span><strong>Password:</strong> Akan di-generate otomatis</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-heroicon-o-arrow-path class="w-4 h-4 mt-0.5 flex-shrink-0 text-info" />
                                        <span><strong>Ganti Password:</strong> Saat login pertama</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-heroicon-o-camera class="w-4 h-4 mt-0.5 flex-shrink-0 text-info" />
                                        <span><strong>Face Recognition:</strong> Untuk absensi</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-heroicon-o-user-circle class="w-4 h-4 mt-0.5 flex-shrink-0 text-info" />
                                        <span><strong>Akun Login:</strong> Otomatis dibuat</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="modal-action sticky bottom-0 bg-base-100 pt-4 border-t mt-6">
                    <button type="button" wire:click="closeModal" class="btn btn-ghost gap-2">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <x-heroicon-o-check class="w-5 h-5" />
                            Simpan Karyawan
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <span class="loading loading-spinner loading-sm"></span>
                            Menyimpan data...
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <label class="modal-backdrop" for="modal-create-karyawan">Close</label>
    </div>
</div>