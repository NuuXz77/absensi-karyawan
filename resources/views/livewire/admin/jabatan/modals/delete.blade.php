<div x-data="modal('modal_delete_jabatan')">
    @teleport('body')
        <dialog id="modal_delete_jabatan" class="modal" wire:ignore.self>
            <div class="modal-box border border-base-300">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold flex items-center gap-2 text-error">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    Konfirmasi Hapus
                </h3>

                <div class="py-4">
                    <p class="text-base mb-4">Apakah Anda yakin ingin menghapus jabatan ini?</p>

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                        {{-- KODE JABATAN --}}
                        <fieldset>
                            <legend class="fieldset-legend">KODE JABATAN</legend>
                            <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                                <x-heroicon-o-hashtag class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="kode_jabatan" class="uppercase" readonly />
                            </label>
                        </fieldset>

                        {{-- NAMA JABATAN --}}
                        <fieldset>
                            <legend class="fieldset-legend">NAMA JABATAN</legend>
                            <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                                <x-heroicon-o-briefcase class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="nama_jabatan" readonly />
                            </label>
                        </fieldset>

                        {{-- DEPARTEMEN --}}
                        <fieldset>
                            <legend class="fieldset-legend">DEPARTEMEN</legend>
                            <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                                <x-heroicon-o-building-office class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="departemen_nama" readonly />
                            </label>
                        </fieldset>

                        {{-- JUMLAH KARYAWAN --}}
                        <fieldset>
                            <legend class="fieldset-legend">JUMLAH KARYAWAN</legend>
                            <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                                <x-heroicon-o-users class="w-4 h-4 opacity-70" />
                                <input type="text" wire:model="karyawan_count" readonly />
                            </label>
                        </fieldset>
                    </div>

                    @if ($karyawan_count > 0)
                        <div class="alert alert-warning mt-4">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            <span>Jabatan ini memiliki {{ $karyawan_count }} karyawan. Pastikan untuk memindahkan karyawan terlebih dahulu!</span>
                        </div>
                    @endif

                    <p class="text-sm opacity-70 mt-4">Tindakan ini tidak dapat dibatalkan.</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-6">
                    {{-- <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                        Batal
                    </button> --}}
                    <button type="button" wire:click="delete" class="btn btn-error gap-2 btn-sm"
                        wire:loading.attr="disabled" wire:target="delete">
                        <span wire:loading.remove wire:target="delete" class="flex items-center gap-2">
                            <x-heroicon-o-trash class="w-5 h-5" />
                            Hapus
                        </span>
                        <span wire:loading wire:target="delete" class="flex items-center gap-2">
                            <span class="loading loading-spinner loading-sm"></span>
                            Menghapus...
                        </span>
                    </button>
                </div>
            </div>
        </dialog>
    @endteleport

    <x-partials.toast 
        :success="$showSuccess ? 'Jabatan berhasil dihapus!' : null" 
        :error="$showError ? ($errorMessage ?: 'Gagal menghapus data!') : null" 
    />
</div>
