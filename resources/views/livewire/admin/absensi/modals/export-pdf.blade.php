<div x-data="modal('modal_export_pdf')">
    <button class="btn btn-info btn-sm gap-2" @click="openModal()">
        <x-bi-file-earmark-pdf class="w-5 h-5" />
        <span class="hidden sm:inline">Export PDF</span>
    </button>
    
    @teleport('body')
        <dialog id="modal_export_pdf" class="modal" wire:ignore.self x-show="open">
            <div class="modal-box max-w-4xl border border-base-300">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                
                <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <x-bi-file-earmark-pdf class="w-6 h-6 text-info" />
                    Export Data Absensi ke PDF
                </h3>
                
                <form wire:submit.prevent="export" class="space-y-6">
                    <!-- First Row: Karyawan and Tipe Periode -->
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Nama Karyawan -->
                        <fieldset>
                            <legend class="fieldset-legend">
                                <x-heroicon-o-user class="w-4 h-4" />
                                NAMA KARYAWAN
                            </legend>
                            <select wire:model.live="karyawan_id" class="select select-bordered w-full">
                                <option value="">Semua Karyawan</option>
                                @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }} - {{ $karyawan->nip }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs opacity-60 mt-1">Kosongkan untuk export semua karyawan</p>
                        </fieldset>
                        
                        <!-- Pilihan Tipe Periode -->
                        <fieldset>
                            <legend class="fieldset-legend">
                                <x-heroicon-o-calendar-days class="w-4 h-4" />
                                TIPE PERIODE
                            </legend>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="tipe_periode" value="tanggal" class="radio radio-primary radio-sm" />
                                    <span class="text-sm">Tanggal Spesifik</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="tipe_periode" value="jangka_waktu" class="radio radio-primary radio-sm" />
                                    <span class="text-sm">Jangka Waktu</span>
                                </label>
                            </div>
                        </fieldset>
                    </div>
                    
                    <!-- Period Selection Section -->
                    <div class="bg-base-100 p-4 rounded-lg border border-base-300">
                        @if($tipe_periode === 'tanggal')
                            <!-- Tanggal Spesifik -->
                            <fieldset>
                                <legend class="fieldset-legend">
                                    <x-heroicon-o-calendar class="w-4 h-4" />
                                    RANGE TANGGAL
                                </legend>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs opacity-70 mb-1 block">Tanggal Awal *</label>
                                        <input type="date" 
                                               wire:model.live="tanggal_awal" 
                                               class="input input-bordered w-full" 
                                               required />
                                    </div>
                                    <div>
                                        <label class="text-xs opacity-70 mb-1 block">Tanggal Akhir</label>
                                        <input type="date" 
                                               wire:model.live="tanggal_akhir" 
                                               class="input input-bordered w-full" />
                                    </div>
                                </div>
                                <p class="text-xs opacity-60 mt-2">
                                    <x-heroicon-o-information-circle class="w-3 h-3 inline" />
                                    Jika tanggal akhir kosong, export hanya untuk tanggal awal saja
                                </p>
                            </fieldset>
                        @else
                            <!-- Jangka Waktu -->
                            <div class="space-y-4">
                                <!-- Pilihan Jangka Waktu -->
                                <fieldset>
                                    <legend class="fieldset-legend">
                                        <x-heroicon-o-clock class="w-4 h-4" />
                                        JANGKA WAKTU
                                    </legend>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" wire:model.live="jangka_waktu" value="mingguan" class="radio radio-primary radio-sm" />
                                            <span class="text-sm">Mingguan</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" wire:model.live="jangka_waktu" value="bulanan" class="radio radio-primary radio-sm" />
                                            <span class="text-sm">Bulanan</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" wire:model.live="jangka_waktu" value="tahunan" class="radio radio-primary radio-sm" />
                                            <span class="text-sm">Tahunan</span>
                                        </label>
                                    </div>
                                </fieldset>
                                
                                <!-- Input fields based on selection -->
                                @if($jangka_waktu === 'mingguan')
                                    <!-- Mingguan -->
                                    <div class="grid grid-cols-4 gap-3">
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Tahun *</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_minggu" 
                                                   min="2000" 
                                                   max="2099"
                                                   class="input input-bordered w-full" 
                                                   required />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Bulan *</label>
                                            <select wire:model.live="bulan_minggu" class="select select-bordered w-full" required>
                                                @foreach(range(1, 12) as $bulan)
                                                    <option value="{{ $bulan }}">{{ \Carbon\Carbon::create()->month($bulan)->locale('id')->monthName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Minggu Awal *</label>
                                            <input type="number" 
                                                   wire:model.live="minggu_awal" 
                                                   min="1" 
                                                   max="5"
                                                   class="input input-bordered w-full" 
                                                   required />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Minggu Akhir *</label>
                                            <input type="number" 
                                                   wire:model.live="minggu_akhir" 
                                                   min="1" 
                                                   max="5"
                                                   class="input input-bordered w-full" 
                                                   required />
                                        </div>
                                    </div>
                                @elseif($jangka_waktu === 'bulanan')
                                    <!-- Bulanan -->
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Tahun *</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_bulan" 
                                                   min="2000" 
                                                   max="2099"
                                                   class="input input-bordered w-full" 
                                                   required />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Bulan Awal *</label>
                                            <select wire:model.live="bulan_awal" class="select select-bordered w-full" required>
                                                @foreach(range(1, 12) as $bulan)
                                                    <option value="{{ $bulan }}">{{ \Carbon\Carbon::create()->month($bulan)->locale('id')->monthName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Bulan Akhir *</label>
                                            <select wire:model.live="bulan_akhir" class="select select-bordered w-full" required>
                                                @foreach(range(1, 12) as $bulan)
                                                    <option value="{{ $bulan }}">{{ \Carbon\Carbon::create()->month($bulan)->locale('id')->monthName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <!-- Tahunan -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Tahun Awal *</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_awal" 
                                                   min="2000" 
                                                   max="2099"
                                                   class="input input-bordered w-full" 
                                                   required />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">Tahun Akhir *</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_akhir" 
                                                   min="2000" 
                                                   max="2099"
                                                   class="input input-bordered w-full" 
                                                   required />
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Periode Text Info -->
                        @if($tipe_periode === 'jangka_waktu')
                            <div class="mt-4 p-3 bg-base-200 rounded-lg">
                                <div class="flex items-center gap-2 text-sm">
                                    <x-heroicon-o-information-circle class="w-4 h-4 text-info" />
                                    <span><strong>Periode:</strong> {{ $periodeText ?: 'Pilih periode untuk melihat range' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Third Row: Nama File and Total Data -->
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Nama File -->
                        <fieldset>
                            <legend class="fieldset-legend">
                                <x-heroicon-o-document-text class="w-4 h-4" />
                                NAMA FILE
                            </legend>
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       wire:model.live="nama_file" 
                                       class="input input-bordered flex-1" 
                                       placeholder="Nama file otomatis..." />
                                <span class="text-xs opacity-60">.pdf</span>
                            </div>
                            <p class="text-xs opacity-60 mt-1">Nama file akan otomatis dibuat berdasarkan filter (bisa custom)</p>
                        </fieldset>
                        
                        <!-- Total Data -->
                        <fieldset>
                            <legend class="fieldset-legend">
                                <x-heroicon-o-clipboard-document-check class="w-4 h-4" />
                                TOTAL DATA
                            </legend>
                            <div class="bg-base-200 border border-base-300 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-info text-info-content rounded-full w-12">
                                                <span class="text-lg font-bold">{{ $totalAbsensi }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold">Data Absensi</p>
                                            <p class="text-xs opacity-60">yang akan diekspor</p>
                                        </div>
                                    </div>
                                    @if($totalAbsensi > 0)
                                        <x-heroicon-o-check-circle class="w-6 h-6 text-success" />
                                    @else
                                        <x-heroicon-o-exclamation-circle class="w-6 h-6 text-warning" />
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-between gap-3 pt-4 border-t border-base-300">
                        <button type="button" 
                                wire:click="resetFilters" 
                                class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                            Reset Filter
                        </button>
                        
                        <div class="flex gap-2">
                            <button type="button" 
                                    @click="open = false" 
                                    class="btn btn-outline btn-sm gap-2">
                                Batal
                            </button>
                            
                            <button type="submit" 
                                    class="btn btn-info btn-sm gap-2" 
                                    wire:loading.attr="disabled" 
                                    wire:target="export"
                                    {{ $totalAbsensi == 0 ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="export" class="flex items-center gap-2">
                                    <x-bi-file-earmark-pdf class="w-4 h-4" />
                                    Export PDF
                                </span>
                                <span wire:loading wire:target="export" class="flex items-center gap-2">
                                    <span class="loading loading-spinner loading-sm"></span>
                                    Mengekspor...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </dialog>
    @endteleport
</div>
