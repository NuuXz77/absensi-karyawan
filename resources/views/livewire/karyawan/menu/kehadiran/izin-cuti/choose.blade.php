<div x-data="modal('modal_choose_izin_cuti')">
    <button type="button"  @click="openModal()"
        class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
        <div class="flex items-center gap-4">
            <div
                class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                <x-heroicon-o-document-text class="h-5 w-5 text-primary" />
            </div>
            <div class="text-left">
                <h3 class="font-semibold text-base-content">Pengajuan Izin / Cuti</h3>
                <p class="text-sm text-base-content/60">Ajukan izin atau cuti kerja</p>
            </div>
        </div>
        <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
    </button>
    <dialog id="modal_choose_izin_cuti" class="modal" wire:ignore.self>
        <div class="modal-box max-w-2xl border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                    wire:click="closeModal">✕</button>
            </form>
            <h3 class="text-lg font-bold mb-6">Pilih Jenis Pengajuan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Card Ajukan Izin -->
                <div class="card bg-base-100 border-2 border-base-300 hover:border-primary hover:shadow-lg transition-all cursor-pointer"
                    wire:click="goToIzin">
                    <div class="card-body items-center text-center">
                        <div class="w-20 h-20 rounded-full bg-warning/10 flex items-center justify-center mb-4">
                            <x-heroicon-o-clipboard-document-check class="w-10 h-10 text-warning" />
                        </div>
                        <h4 class="card-title text-lg">Ajukan Izin</h4>
                        <p class="text-sm text-base-content/70">Pengajuan izin sakit, keperluan pribadi, dinas, atau
                            lainnya</p>
                        <div class="card-actions mt-4">
                            <button type="button" class="btn btn-warning btn-sm gap-2" wire:click="goToIzin">
                                <x-heroicon-o-arrow-right class="w-4 h-4" />
                                Ajukan Izin
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card Ajukan Cuti -->
                <div class="card bg-base-100 border-2 border-base-300 hover:border-primary hover:shadow-lg transition-all cursor-pointer"
                    wire:click="goToCuti">
                    <div class="card-body items-center text-center">
                        <div class="w-20 h-20 rounded-full bg-info/10 flex items-center justify-center mb-4">
                            <x-heroicon-o-calendar-days class="w-10 h-10 text-info" />
                        </div>
                        <h4 class="card-title text-lg">Ajukan Cuti</h4>
                        <p class="text-sm text-base-content/70">Pengajuan cuti tahunan atau cuti khusus</p>
                        <div class="card-actions mt-4">
                            <button type="button" class="btn btn-info btn-sm gap-2" wire:click="goToCuti">
                                <x-heroicon-o-arrow-right class="w-4 h-4" />
                                Ajukan Cuti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </dialog>
</div>

{{-- <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-choose-modal', (event) => {
            document.getElementById('modal_choose_izin_cuti').showModal();
        });

        Livewire.on('close-choose-modal', () => {
            document.getElementById('modal_choose_izin_cuti').close();
        });
    });
</script> --}}
