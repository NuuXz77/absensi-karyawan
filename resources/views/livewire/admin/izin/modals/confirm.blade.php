<div>
    <dialog id="modal_confirm_izin" class="modal" wire:ignore.self>
        <div class="modal-box border border-base-300 max-w-3xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold flex items-center gap-2">
                @if ($confirmAction === 'approve')
                    <x-heroicon-o-check-circle class="w-6 h-6 text-success" />
                    Setujui Pengajuan Izin
                @else
                    <x-heroicon-o-x-circle class="w-6 h-6 text-error" />
                    Tolak Pengajuan Izin
                @endif
            </h3>

            <div class="divider"></div>

            @if ($confirmAction === 'approve')
                <div class="alert alert-success">
                    <x-heroicon-o-information-circle class="w-6 h-6" />
                    <div>
                        <h4 class="font-bold">Konfirmasi Persetujuan</h4>
                        <p class="text-sm">Izin karyawan akan disetujui dan saldo izin akan dikurangi.</p>
                    </div>
                </div>
            @else
                <div class="alert alert-error">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    <div>
                        <h4 class="font-bold">Konfirmasi Penolakan</h4>
                        <p class="text-sm">Izin karyawan akan ditolak dan tidak diproses lebih lanjut.</p>
                    </div>
                </div>
            @endif

            <div class="mt-4 space-y-3">
                <p class="text-base-content/80">
                    @if ($confirmAction === 'approve')
                        Apakah Anda yakin ingin menyetujui pengajuan izin ini?
                    @else
                        Apakah Anda yakin ingin menolak pengajuan izin ini?
                    @endif
                </p>

                <div class="space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <fieldset>
                            <legend class="fieldset-legend">KARYAWAN</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="nama_lengkap" disabled />
                            </label>
                        </fieldset>

                        <fieldset>
                            <legend class="fieldset-legend">DEPARTEMEN</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="nama_departemen" disabled />
                            </label>
                        </fieldset>
                        <fieldset>
                            <legend class="fieldset-legend">TANGGAL MULAI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="tanggal_mulai" disabled />
                            </label>
                        </fieldset>

                        <fieldset>
                            <legend class="fieldset-legend">TANGGAL SELESAI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="tanggal_selesai" disabled />
                            </label>
                        </fieldset>
                        <fieldset>
                            <legend class="fieldset-legend">DURASI</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="durasi" disabled />
                            </label>
                        </fieldset>

                        <fieldset>
                            <legend class="fieldset-legend">JENIS IZIN</legend>
                            <label class="input w-full input-bordered flex items-center gap-2">
                                <x-heroicon-o-document-text class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="jenis_izin" disabled />
                            </label>
                        </fieldset>
                    </div>


                    <fieldset>
                        <legend class="fieldset-legend">ALASAN</legend>
                        <label class="input w-full input-bordered flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-right class="w-4 h-4 opacity-70" />
                            <input type="text" wire:model="alasan" disabled />
                        </label>
                    </fieldset>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
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
        </div>
    </dialog>

    <x-partials.toast :success="$showSuccess ? ($confirmAction === 'approve' ? 'Izin berhasil disetujui dan saldo telah diperbarui!' : 'Izin berhasil ditolak!') : null" :error="$showError ? ($errorMessage ?: 'Gagal memproses izin!') : null" />
</div>

{{-- <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-confirm-modal', (event) => {
            document.getElementById('modal_confirm_izin').showModal();
        });

        Livewire.on('close-confirm-modal', () => {
            document.getElementById('modal_confirm_izin').close();
        });
    });
</script> --}}
