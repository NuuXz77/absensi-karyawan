<div>
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <div class="flex items-center justify-between mb-6">
                <h2 class="card-title text-2xl">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-info" />
                    Form Pengajuan Cuti
                </h2>
                <a wire:navigate href="{{ route('karyawan.menu.index') }}" class="btn btn-ghost btn-sm gap-2">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                    Kembali
                </a>
            </div>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- TANGGAL MULAI --}}
                    <fieldset>
                        <legend class="fieldset-legend">TANGGAL MULAI CUTI</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('tanggal_mulai') input-error @enderror">
                            <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                            <input required type="date" wire:model.live="tanggal_mulai" class="grow" />
                        </label>
                        @error('tanggal_mulai')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- TANGGAL SELESAI --}}
                    <fieldset>
                        <legend class="fieldset-legend">TANGGAL SELESAI CUTI</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('tanggal_selesai') input-error @enderror">
                            <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                            <input required type="date" wire:model.live="tanggal_selesai" class="grow" />
                        </label>
                        @error('tanggal_selesai')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- JUMLAH HARI (AUTO CALCULATED) --}}
                    <fieldset>
                        <legend class="fieldset-legend">JUMLAH HARI</legend>
                        <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                            <x-heroicon-o-calculator class="w-4 h-4 opacity-70" />
                            <input type="number" wire:model="jumlah_hari" readonly class="grow bg-transparent" placeholder="Otomatis terhitung" />
                        </label>
                        <p class="text-xs text-base-content/60 mt-1">
                            <x-heroicon-o-information-circle class="w-3 h-3 inline" />
                            Akan otomatis terhitung berdasarkan tanggal mulai dan selesai
                        </p>
                    </fieldset>

                    {{-- JENIS CUTI --}}
                    <fieldset>
                        <legend class="fieldset-legend">JENIS CUTI</legend>
                        <label class="w-full">
                            <select required wire:model="jenis_cuti" class="select select-bordered w-full @error('jenis_cuti') select-error @enderror">
                                <option value="">Pilih Jenis Cuti</option>
                                <option value="tahunan">Cuti Tahunan</option>
                                <option value="khusus">Cuti Khusus</option>
                            </select>
                        </label>
                        @error('jenis_cuti')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- KETERANGAN --}}
                    <fieldset class="md:col-span-2">
                        <legend class="fieldset-legend">KETERANGAN</legend>
                        <textarea required wire:model="keterangan" 
                            class="textarea textarea-bordered w-full @error('keterangan') textarea-error @enderror" 
                            placeholder="Jelaskan keperluan cuti Anda..." 
                            rows="4"></textarea>
                        @error('keterangan')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- BUKTI FOTO --}}
                    <fieldset class="md:col-span-2">
                        <legend class="fieldset-legend">BUKTI PENDUKUNG (OPSIONAL)</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('bukti_foto') input-error @enderror">
                            <x-heroicon-o-photo class="w-4 h-4 opacity-70" />
                            <input type="file" wire:model="bukti_foto" accept="image/*" class="grow file-input file-input-bordered file-input-sm w-full" />
                        </label>
                        <p class="text-xs text-base-content/60 mt-1">
                            <x-heroicon-o-information-circle class="w-3 h-3 inline" />
                            Upload dokumen pendukung jika diperlukan (Max: 2MB, Format: JPG, PNG)
                        </p>
                        @error('bukti_foto')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        @if($bukti_foto)
                            <div class="mt-3">
                                <img src="{{ $bukti_foto->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-lg border border-base-300" alt="Preview">
                            </div>
                        @endif
                    </fieldset>

                    {{-- SISA SALDO CUTI INFO --}}
                    @if(isset($sisaSaldoCuti))
                    <fieldset class="md:col-span-2">
                        <div class="alert alert-info">
                            <x-heroicon-o-information-circle class="w-5 h-5" />
                            <div class="flex-1">
                                <div class="font-semibold mb-2">Informasi Saldo Cuti Anda Tahun {{ date('Y') }}</div>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <div class="text-xs opacity-70">Total Jatah</div>
                                        <div class="font-bold text-lg">{{ $totalCuti }} hari</div>
                                    </div>
                                    <div>
                                        <div class="text-xs opacity-70">Terpakai</div>
                                        <div class="font-bold text-lg">{{ $totalCuti - $sisaSaldoCuti }} hari</div>
                                    </div>
                                    <div>
                                        <div class="text-xs opacity-70">Sisa</div>
                                        <div class="font-bold text-lg">{{ $sisaSaldoCuti }} hari</div>
                                    </div>
                                </div>
                                <progress class="progress progress-info w-full mt-2" value="{{ $totalCuti - $sisaSaldoCuti }}" max="{{ $totalCuti }}"></progress>
                            </div>
                        </div>
                    </fieldset>
                    @endif

                </div>

                {{-- Info Alert --}}
                <div class="card bg-info/10 border-info/20 mt-6">
                    <div class="card-body p-4">
                        <div class="flex items-start gap-3">
                            <x-heroicon-o-information-circle class="w-5 h-5 text-info flex-shrink-0 mt-0.5" />
                            <div class="text-sm">
                                <p class="font-semibold text-info mb-2">Informasi Pengajuan Cuti:</p>
                                <ul class="list-disc list-inside space-y-1 text-info/80">
                                    <li><strong>Cuti Tahunan:</strong> Cuti rutin yang diberikan setiap tahun (biasanya 12 hari/tahun)</li>
                                    <li><strong>Cuti Khusus:</strong> Cuti untuk keperluan khusus (pernikahan, kelahiran anak, dll)</li>
                                    <li>Pastikan saldo cuti Anda mencukupi untuk jumlah hari yang diajukan</li>
                                    <li>Pengajuan cuti sebaiknya diajukan minimal 3 hari sebelum tanggal cuti</li>
                                    <li>Status pengajuan dapat dilihat di riwayat cuti</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3 mt-6">
                    <a wire:navigate href="{{ route('karyawan.menu.index') }}" class="btn btn-ghost btn-sm gap-2">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                        Batal
                    </a>
                    <button type="submit" class="btn btn-info gap-2 btn-sm" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                            <x-heroicon-o-paper-airplane class="w-5 h-5" />
                            Ajukan Cuti
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <span class="loading loading-spinner loading-sm"></span>
                            Mengirim pengajuan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Pengajuan cuti berhasil dikirim!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal mengirim pengajuan!' }}</span>
            </div>
        @endif
    </div>
</div>
