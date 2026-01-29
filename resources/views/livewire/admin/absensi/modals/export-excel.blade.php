<div x-data="modal('modal_export_excel')">
    <button class="btn btn-primary btn-sm gap-2" @click="openModal()">
        <x-bi-file-earmark-excel class="w-5 h-5" />
        <span class="hidden sm:inline">Export Excel</span>
    </button>
    
    @teleport('body')
        <dialog id="modal_export_excel" class="modal" wire:ignore.self x-show="open">
            <div class="modal-box max-w-4xl border border-base-300"> <!-- Changed to max-w-4xl (3xl) -->
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                
                <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <x-bi-file-earmark-excel class="w-6 h-6 text-success" /> <!-- Slightly larger icon -->
                    Export Data Absensi
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
                                               required 
                                               class="input input-bordered w-full" />
                                    </div>
                                    <div>
                                        <label class="text-xs opacity-70 mb-1 block">Tanggal Akhir</label>
                                        <input type="date" 
                                               wire:model.live="tanggal_akhir" 
                                               min="{{ $tanggal_awal }}" 
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
                                            <label class="text-xs opacity-70 mb-1 block">TAHUN</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_minggu" 
                                                   min="2020" 
                                                   max="2100" 
                                                   class="input input-bordered w-full" />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">BULAN</label>
                                            <select wire:model.live="bulan_minggu" class="select select-bordered w-full">
                                                <option value="1">Januari</option>
                                                <option value="2">Februari</option>
                                                <option value="3">Maret</option>
                                                <option value="4">April</option>
                                                <option value="5">Mei</option>
                                                <option value="6">Juni</option>
                                                <option value="7">Juli</option>
                                                <option value="8">Agustus</option>
                                                <option value="9">September</option>
                                                <option value="10">Oktober</option>
                                                <option value="11">November</option>
                                                <option value="12">Desember</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">MINGGU AWAL</label>
                                            <input type="number" 
                                                   wire:model.live="minggu_awal" 
                                                   min="1" 
                                                   max="5" 
                                                   class="input input-bordered w-full" />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">MINGGU AKHIR</label>
                                            <input type="number" 
                                                   wire:model.live="minggu_akhir" 
                                                   min="{{ $minggu_awal }}" 
                                                   max="5" 
                                                   class="input input-bordered w-full" />
                                        </div>
                                    </div>
                                @elseif($jangka_waktu === 'bulanan')
                                    <!-- Bulanan -->
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">TAHUN</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_bulan" 
                                                   min="2020" 
                                                   max="2100" 
                                                   class="input input-bordered w-full" />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">BULAN AWAL</label>
                                            <select wire:model.live="bulan_awal" class="select select-bordered w-full">
                                                <option value="1">Januari</option>
                                                <option value="2">Februari</option>
                                                <option value="3">Maret</option>
                                                <option value="4">April</option>
                                                <option value="5">Mei</option>
                                                <option value="6">Juni</option>
                                                <option value="7">Juli</option>
                                                <option value="8">Agustus</option>
                                                <option value="9">September</option>
                                                <option value="10">Oktober</option>
                                                <option value="11">November</option>
                                                <option value="12">Desember</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">BULAN AKHIR</label>
                                            <select wire:model.live="bulan_akhir" class="select select-bordered w-full">
                                                @for($i = $bulan_awal; $i <= 12; $i++)
                                                    <option value="{{ $i }}">
                                                        {{ \Carbon\Carbon::create()->month($i)->locale('id')->monthName }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <!-- Tahunan -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">TAHUN AWAL</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_awal" 
                                                   min="2020" 
                                                   max="2100" 
                                                   class="input input-bordered w-full" />
                                        </div>
                                        <div>
                                            <label class="text-xs opacity-70 mb-1 block">TAHUN AKHIR</label>
                                            <input type="number" 
                                                   wire:model.live="tahun_akhir" 
                                                   min="{{ $tahun_awal }}" 
                                                   max="2100" 
                                                   class="input input-bordered w-full" />
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
                                <span class="text-xs opacity-60">.xlsx</span>
                            </div>
                            <p class="text-xs opacity-60 mt-1">Nama file akan otomatis dibuat berdasarkan filter (bisa costum)</p>
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
                                        <div class="p-3 rounded-lg {{ $totalAbsensi > 0 ? 'bg-success' : 'bg-error' }}">
                                            <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-base-100" />
                                        </div>
                                        <div>
                                            <p class="text-xs opacity-60">Total Data Absensi</p>
                                            <p class="text-2xl font-bold">{{ number_format($totalAbsensi) }}</p>
                                        </div>
                                    </div>
                                    @if($totalAbsensi > 0)
                                        <div class="badge badge-success gap-1">
                                            <x-heroicon-o-check class="w-4 h-4" />
                                            Siap Export
                                        </div>
                                    @else
                                        <div class="badge badge-error gap-1">
                                            <x-heroicon-o-x-mark class="w-4 h-4" />
                                            Tidak Ada Data
                                        </div>
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
                                    class="btn btn-success btn-sm gap-2" 
                                    wire:loading.attr="disabled" 
                                    wire:target="export"
                                    {{ $totalAbsensi == 0 ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="export" class="flex items-center gap-2">
                                    <x-bi-file-earmark-excel class="w-4 h-4" />
                                    Export Excel
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