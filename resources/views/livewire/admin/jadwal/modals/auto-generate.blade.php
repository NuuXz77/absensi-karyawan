<div x-data="modal('modal_auto_generate_jadwal')">
    <button class="btn btn-success btn-sm gap-2" @click="openModal()">
        <x-heroicon-o-sparkles class="w-5 h-5" />
        <span class="hidden sm:inline">Auto Generate Jadwal</span>
    </button>
    <dialog id="modal_auto_generate_jadwal" class="modal" wire:ignore.self>
        <div class="modal-box max-w-3xl border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold flex items-center gap-2">
                <x-heroicon-o-sparkles class="w-6 h-6 text-success" />
                Auto Generate Jadwal Kerja
            </h3>
            <p class="text-sm opacity-70 mt-2">Generate jadwal otomatis untuk semua karyawan di departemen</p>

            <form wire:submit.prevent="generate" class="mt-4">
                <div class="grid grid-cols-2 gap-4">
                    {{-- PERIODE --}}
                    <fieldset>
                        <legend class="fieldset-legend">TANGGAL MULAI</legend>
                        <input type="date" wire:model="tanggal_mulai"
                            class="input input-bordered w-full @error('tanggal_mulai') input-error @enderror" />
                        @error('tanggal_mulai')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <legend class="fieldset-legend">TANGGAL SELESAI</legend>
                        <input type="date" wire:model="tanggal_selesai"
                            class="input input-bordered w-full @error('tanggal_selesai') input-error @enderror" />
                        @error('tanggal_selesai')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- DEPARTEMEN --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">DEPARTEMEN</legend>
                        <select wire:model.live="departemen_id"
                            class="select select-bordered w-full @error('departemen_id') select-error @enderror">
                            <option value="">Pilih Departemen</option>
                            @foreach ($departemens as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                        @error('departemen_id')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @if ($departemen_id && count($karyawan_ids) > 0)
                            <p class="text-xs text-success mt-1">
                                <x-heroicon-o-check-circle class="w-3 h-3 inline" />
                                {{ count($karyawan_ids) }} karyawan akan di-generate
                            </p>
                        @endif
                    </fieldset>

                    {{-- POLA SHIFT --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">POLA SHIFT</legend>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                class="cursor-pointer label justify-start gap-3 bg-base-200 rounded-lg p-3 border-2 {{ $shift_pattern === 'single' ? 'border-primary' : 'border-transparent' }}">
                                <input type="radio" wire:model.live="shift_pattern" value="single"
                                    class="radio radio-primary" />
                                <div>
                                    <span class="label-text font-semibold">Single Shift</span>
                                    <p class="text-xs opacity-70">Semua karyawan shift yang sama</p>
                                </div>
                            </label>
                            <label
                                class="cursor-pointer label justify-start gap-3 bg-base-200 rounded-lg p-3 border-2 {{ $shift_pattern === 'rotating' ? 'border-primary' : 'border-transparent' }}">
                                <input type="radio" wire:model.live="shift_pattern" value="rotating"
                                    class="radio radio-primary" />
                                <div>
                                    <span class="label-text font-semibold">Rotating Shift</span>
                                    <p class="text-xs opacity-70">Shift bergantian setiap hari</p>
                                </div>
                            </label>
                        </div>
                    </fieldset>

                    {{-- PILIH SHIFT --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">
                            PILIH SHIFT
                            @if ($shift_pattern === 'rotating')
                                <span class="text-xs opacity-70">(Urutan dari atas ke bawah)</span>
                            @endif
                        </legend>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($shifts as $shift)
                                <label
                                    class="cursor-pointer label justify-start gap-3 bg-base-200 rounded-lg p-3 hover:bg-base-300">
                                    <input type="checkbox" wire:model="selected_shifts" value="{{ $shift->id }}"
                                        class="checkbox checkbox-primary" />
                                    <div class="flex-1">
                                        <span class="label-text font-semibold">{{ $shift->nama_shift }}</span>
                                        <p class="text-xs opacity-70">
                                            {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('selected_shifts')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- LOKASI --}}
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">LOKASI <span class="text-error">*</span></legend>
                        <select wire:model="lokasi_id"
                            class="select select-bordered w-full @error('lokasi_id') select-error @enderror">
                            <option value="">Pilih Lokasi</option>
                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}">
                                    {{ $lokasi->nama_lokasi }} ({{ $lokasi->radius_meter }}m)
                                </option>
                            @endforeach
                        </select>
                        @error('lokasi_id')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    {{-- OPTIONS --}}
                    <fieldset class="col-span-2">
                        <label class="cursor-pointer label justify-start gap-3">
                            <input type="checkbox" wire:model="include_weekends" class="checkbox checkbox-primary" />
                            <span class="label-text">
                                <span class="font-semibold">Include Weekends</span>
                                <p class="text-xs opacity-70">Generate jadwal juga untuk Sabtu & Minggu</p>
                            </span>
                        </label>
                    </fieldset>

                    {{-- PREVIEW --}}
                    @if ($tanggal_mulai && $tanggal_selesai && $departemen_id && count($selected_shifts) > 0)
                        <fieldset class="col-span-2">
                            <div class="alert alert-info">
                                <x-heroicon-o-information-circle class="w-5 h-5" />
                                <div class="text-sm">
                                    <p class="font-semibold">Preview Generate:</p>
                                    <p>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</p>
                                    <p>Total Karyawan: {{ count($karyawan_ids) }}</p>
                                    <p>Shift: {{ count($selected_shifts) }} shift
                                        {{ $shift_pattern === 'rotating' ? '(rotating)' : '(single)' }}</p>
                                </div>
                            </div>
                        </fieldset>
                    @endif

                    {{-- ACTIONS --}}
                    <div class="col-span-2 flex justify-end gap-3 mt-4">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success btn-sm gap-2" wire:loading.attr="disabled"
                            wire:target="generate">
                            <span wire:loading.remove wire:target="generate" class="flex items-center gap-2">
                                <x-heroicon-o-sparkles class="w-5 h-5" />
                                Generate Jadwal
                            </span>
                            <span wire:loading wire:target="generate" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-sm"></span>
                                Generating...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Toast Notifications --}}
    <div class="toast toast-start z-[9999]">
        @if ($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center"
                x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-check class="w-5" />
                <span>{{ $successMessage }}</span>
            </div>
        @endif

        @if ($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center"
                x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5" />
                <span>{{ $errorMessage }}</span>
            </div>
        @endif
    </div>
</div>
