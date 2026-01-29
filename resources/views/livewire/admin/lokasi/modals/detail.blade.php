<div x-data="modal('modal_detail_lokasi')">
    <dialog id="modal_detail_lokasi" class="modal" wire:ignore.self>
        <div class="modal-box max-w-3xl border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="modal_detail_lokasi.close()">✕</button>
            </form>
            <h3 class="text-lg font-bold">Detail Lokasi</h3>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <!-- Nama Lokasi -->
                <fieldset>
                    <legend class="fieldset-legend">NAMA LOKASI</legend>
                    <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                        <x-heroicon-o-map-pin class="w-4 h-4 opacity-70" />
                        <input type="text" wire:model="nama_lokasi" readonly />
                    </label>
                </fieldset>

                <!-- Radius -->
                <fieldset>
                    <legend class="fieldset-legend">RADIUS (METER)</legend>
                    <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                        <x-heroicon-o-arrow-path class="w-4 h-4 opacity-70" />
                        <input type="text" wire:model="radius_meter" readonly />
                    </label>
                </fieldset>

                <!-- Latitude -->
                <fieldset>
                    <legend class="fieldset-legend">LATITUDE</legend>
                    <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                        <x-heroicon-o-map class="w-4 h-4 opacity-70" />
                        <input type="text" wire:model="latitude" readonly />
                    </label>
                </fieldset>

                <!-- Longitude -->
                <fieldset>
                    <legend class="fieldset-legend">LONGITUDE</legend>
                    <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                        <x-heroicon-o-map class="w-4 h-4 opacity-70" />
                        <input type="text" wire:model="longitude" readonly />
                    </label>
                </fieldset>

                <!-- Map - Full Width -->
                <fieldset class="col-span-2">
                    <legend class="fieldset-legend">LOKASI DI PETA</legend>
                    <div id="detail_map" class="w-full h-96 rounded-lg border border-base-300" wire:ignore></div>
                </fieldset>

                <!-- Status -->
                <fieldset>
                    <legend class="fieldset-legend">STATUS</legend>
                    <label class="input w-full input-bordered flex items-center gap-2 bg-base-200">
                        <x-heroicon-o-check-circle class="w-4 h-4 opacity-70" />
                        <input type="text" value="{{ ucfirst($status) }}" readonly />
                    </label>
                </fieldset>

                <!-- Form Actions -->
                <div class="col-span-2 flex justify-end">
                    <button type="button" class="btn btn-ghost" onclick="modal_detail_lokasi.close()">Tutup</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button onclick="modal_detail_lokasi.close()">close</button>
        </form>
    </dialog>
</div>

<script>
    let detailMap = null;
    let detailMarker = null;

    window.initDetailMap = function(lat, lng, radius) {
        // Pastikan Leaflet sudah dimuat
        if (typeof L === 'undefined') {
            console.error('Leaflet not loaded');
            setTimeout(() => initDetailMap(lat, lng, radius), 100);
            return;
        }

        // Hapus peta lama jika ada
        if (detailMap) {
            detailMap.remove();
            detailMap = null;
            detailMarker = null;
        }

        try {
            // Buat peta baru (readonly - no interaction)
            detailMap = L.map('detail_map', {
                tap: false,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                boxZoom: false,
                keyboard: false,
                zoomControl: true
            }).setView([lat, lng], 15);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
                detectRetina: true
            }).addTo(detailMap);

            // Tambahkan marker
            detailMarker = L.marker([lat, lng]).addTo(detailMap);
            detailMarker.bindPopup('<b>Lokasi</b>').openPopup();

            // Tambahkan radius circle
            L.circle([lat, lng], {
                color: 'blue',
                fillColor: '#30f',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(detailMap);

            // Pastikan peta di-render dengan benar
            setTimeout(() => {
                detailMap.invalidateSize();
                console.log('Detail map initialized successfully');
            }, 300);

        } catch (e) {
            console.error('Error initializing detail map:', e);
        }
    }

    // Livewire listeners
    document.addEventListener('livewire:init', () => {
        Livewire.on('openDetailModal', (data) => {
            // Tampilkan modal
            modal_detail_lokasi.showModal();
            
            // Tunggu modal benar-benar terbuka sebelum inisialisasi peta
            setTimeout(() => {
                const locationData = data[0];
                if (locationData) {
                    const radius = @this.radius_meter || 100;
                    initDetailMap(
                        parseFloat(locationData.latitude) || -6.200000,
                        parseFloat(locationData.longitude) || 106.816666,
                        radius
                    );
                }
            }, 500);
        });
        
        Livewire.on('closeDetailModal', () => {
            modal_detail_lokasi.close();
        });
    });
</script>
