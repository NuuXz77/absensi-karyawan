<div>
    <dialog id="modal_confirm_cuti" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold flex items-center gap-2">
                @if ($confirmAction === 'approve')
                    <x-heroicon-o-check-circle class="w-6 h-6 text-success" />
                    Setujui Pengajuan Cuti
                @else
                    <x-heroicon-o-x-circle class="w-6 h-6 text-error" />
                    Tolak Pengajuan Cuti
                @endif
            </h3>

            @if($this->cuti())
                <div class="divider"></div>

                @if ($confirmAction === 'approve')
                <div class="alert alert-success">
                    <x-heroicon-o-information-circle class="w-6 h-6" />
                    <div>
                        <h4 class="font-bold">Konfirmasi Persetujuan</h4>
                        <p class="text-sm">Cuti karyawan akan disetujui dan saldo cuti akan dikurangi.</p>
                    </div>
                </div>
            @else
                <div class="alert alert-error">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    <div>
                        <h4 class="font-bold">Konfirmasi Penolakan</h4>
                        <p class="text-sm">Cuti karyawan akan ditolak dan tidak diproses lebih lanjut.</p>
                    </div>
                </div>
            @endif

            <div class="mt-4 space-y-3">
                <div class="space-y-3">
                    {{-- Karyawan --}}
                    <fieldset>
                        <legend class="fieldset-legend">KARYAWAN</legend>
                        <label class="input w-full input-bordered flex items-center gap-2">
                            <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                            <input type="text" value="{{ $this->cuti->karyawan->nama_lengkap ?? '-' }}" disabled class="bg-base-200" />
                        </label>
                    </fieldset>

                    {{-- Departemen --}}
                    <fieldset>
                        <legend class="fieldset-legend">DEPARTEMEN</legend>
                        <label class="input w-full input-bordered flex items-center gap-2">
                            <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                            <input type="text" value="{{ $this->cuti->karyawan->departemen->nama_departemen ?? '-' }}" disabled class="bg-base-200" />
                        </label>
                    </fieldset>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- Tanggal Mulai --}}
                        <fieldset>
                            <legend class="fieldset-legend">TANGGAL MULAI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ \Carbon\Carbon::parse($this->cuti->tanggal_mulai)->locale('id')->isoFormat('D MMM Y') }}" disabled class="bg-base-200" />
                            </label>
                        </fieldset>

                        {{-- Tanggal Selesai --}}
                        <fieldset>
                            <legend class="fieldset-legend">TANGGAL SELESAI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ \Carbon\Carbon::parse($this->cuti->tanggal_selesai)->locale('id')->isoFormat('D MMM Y') }}" disabled class="bg-base-200" />
                            </label>
                        </fieldset>

                        {{-- Jumlah Hari --}}
                        <fieldset>
                            <legend class="fieldset-legend">JUMLAH HARI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ $this->cuti->jumlah_hari }} hari" disabled class="bg-base-200" />
                            </label>
                        </fieldset>

                        {{-- Jenis Cuti --}}
                        <fieldset>
                            <legend class="fieldset-legend">JENIS CUTI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-tag class="w-4 h-4 opacity-70" />
                                <input type="text" value="{{ match ($this->cuti->jenis_cuti) { 'tahunan' => 'Cuti Tahunan', 'khusus' => 'Cuti Khusus', default => ucfirst($this->cuti->jenis_cuti) } }}" disabled class="bg-base-200" />
                            </label>
                        </fieldset>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    Batal
                </button>
                @if ($confirmAction === 'approve')
                    <button type="button" wire:click="confirmActionSubmit" class="btn btn-success btn-sm gap-2"
                        wire:loading.attr="disabled" wire:target="confirmActionSubmit">
                        <span wire:loading.remove wire:target="confirmActionSubmit" class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-5 h-5" />
                            Setujui
                        </span>
                        <span wire:loading wire:target="confirmActionSubmit" class="flex items-center gap-2">
                            <span class="loading loading-spinner loading-sm"></span>
                            Memproses...
                        </span>
                    </button>
                @else
                    <button type="button" wire:click="confirmActionSubmit" class="btn btn-error btn-sm gap-2"
                        wire:loading.attr="disabled" wire:target="confirmActionSubmit">
                        <span wire:loading.remove wire:target="confirmActionSubmit" class="flex items-center gap-2">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                            Tolak
                        </span>
                        <span wire:loading wire:target="confirmActionSubmit" class="flex items-center gap-2">
                            <span class="loading loading-spinner loading-sm"></span>
                            Memproses...
                        </span>
                    </button>
                @endif
            </div>
            @endif
        </div>
    </dialog>

    <div class="toast toast-start z-[9999]">
        @if ($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center"
                x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>{{ $confirmAction === 'approve' ? 'Cuti berhasil disetujui!' : 'Cuti berhasil ditolak!' }}</span>
            </div>
        @endif

        @if ($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center"
                x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5" />
                <span>{{ $errorMessage ?: 'Gagal memproses data!' }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-confirm-modal', (event) => {
            document.getElementById('modal_confirm_cuti').showModal();
        });

        Livewire.on('close-confirm-modal', () => {
            document.getElementById('modal_confirm_cuti').close();
        });
    });
</script>
