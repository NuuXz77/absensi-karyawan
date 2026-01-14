<div>
    <dialog id="modal_edit_lokasi" class="modal" wire:ignore.self>
        <div class="modal-box max-w-3xl border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="modal_edit_lokasi.close()">✕</button>
            </form>
            <h3 class="text-lg font-bold">Edit Lokasi</h3>

            <form wire:submit.prevent="update">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Nama Lokasi -->
                    <fieldset>
                        <legend class="fieldset-legend">NAMA LOKASI</legend>
                        <label
                            class="input w-full validator input-bordered flex items-center gap-2 @error('nama_lokasi') input-error @enderror">
                            <x-heroicon-o-map-pin class="w-4 h-4 opacity-70" />
                            <input required type="text" wire:model.defer="nama_lokasi" placeholder="Contoh: Kantor Pusat"
                                maxlength="100" />
                        </label>
                        <p class="validator-hint hidden">Nama lokasi wajib diisi</p>
                        @error('nama_lokasi')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <!-- Radius -->
                    <fieldset>
                        <legend class="fieldset-legend">RADIUS (METER)</legend>
                        <label
                            class="input w-full validator input-bordered flex items-center gap-2 @error('radius_meter') input-error @enderror">
                            <x-heroicon-o-arrow-path class="w-4 h-4 opacity-70" />
                            <input required type="number" wire:model.defer="radius_meter" placeholder="Contoh: 100"
                                min="1" step="1" />
                        </label>
                        <p class="validator-hint hidden">Radius wajib diisi</p>
                        @error('radius_meter')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <legend class="fieldset-legend">LATITUDE</legend>
                        <label class="input w-full input-bordered flex items-center gap-2 @error('latitude') input-error @enderror">
                            <x-heroicon-o-map class="w-4 h-4 opacity-70" />
                            <input required type="text" id="edit_latitude" wire:model.defer="latitude" placeholder="-6.200000" readonly />
                        </label>
                        @error('latitude')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <fieldset>
                        <legend class="fieldset-legend">LONGITUDE</legend>
                        <label class="input w-full input-bordered flex items-center gap-2 @error('longitude') input-error @enderror">
                            <x-heroicon-o-map class="w-4 h-4 opacity-70" />
                            <input required type="text" id="edit_longitude" wire:model.defer="longitude" placeholder="106.816666" readonly />
                        </label>
                        @error('longitude')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <!-- Map - Full Width -->
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">PILIH LOKASI DI PETA (KLIK UNTUK MENANDAI)</legend>
                        <div id="edit_map" class="w-full h-56 rounded-lg border border-base-300"
                            wire:ignore></div>
                        <p class="text-xs text-gray-500 mt-2">Klik pada peta untuk menentukan lokasi</p>
                    </fieldset>

                    <!-- Status -->
                    <fieldset>
                        <legend class="fieldset-legend">STATUS</legend>
                        <label class="cursor-pointer label justify-start gap-3">
                            <input type="checkbox" 
                                wire:model.defer="status"
                                value="active"
                                {{ $status === 'active' ? 'checked' : '' }}
                                class="toggle toggle-success" />
                            <span class="label-text flex items-center gap-2">
                                <x-heroicon-o-check-circle class="w-5 h-5 text-success" />
                                Status Aktif
                            </span>
                        </label>
                    </fieldset>
                    <!-- Form Actions -->
                    <div class="modal-action">
                        <button type="button" class="btn btn-ghost"
                            onclick="modal_edit_lokasi.close()">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Update</span>
                            <span wire:loading>Mengupdate...</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button onclick="modal_edit_lokasi.close()">close</button>
        </form>
    </dialog>
    
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Lokasi berhasil diupdate!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal menyimpan data!' }}</span>
            </div>
        @endif
    </div>
</div>

<script>
    let editMap = null;
    let editMarker = null;
    let editMapInitialized = false;

    window.initEditMap = function(lat, lng) {
        // Pastikan Leaflet sudah dimuat
        if (typeof L === 'undefined') {
            console.error('Leaflet not loaded');
            setTimeout(() => initEditMap(lat, lng), 100);
            return;
        }

        // Hapus peta lama jika ada
        if (editMap) {
            editMap.remove();
            editMap = null;
            editMarker = null;
        }

        try {
            // Buat peta baru
            editMap = L.map('edit_map', {
                tap: false, // Untuk kompatibilitas mobile
                dragging: true,
                scrollWheelZoom: true
            }).setView([lat, lng], 15);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
                detectRetina: true
            }).addTo(editMap);

            // Tambahkan marker awal
            editMarker = L.marker([lat, lng], {
                draggable: true // Membuat marker bisa digeser
            }).addTo(editMap);

            // Event untuk drag marker
            editMarker.on('dragend', function(e) {
                const position = editMarker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });

            // Event untuk klik peta
            editMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                // Pindahkan marker ke lokasi yang diklik
                editMarker.setLatLng([lat, lng]);
                
                // Update koordinat
                updateCoordinates(lat, lng);
            });

            // Fungsi untuk update koordinat
            function updateCoordinates(lat, lng) {
                // Update input fields
                document.getElementById('edit_latitude').value = lat.toFixed(6);
                document.getElementById('edit_longitude').value = lng.toFixed(6);
                
                // Trigger Livewire update dengan object
                Livewire.dispatch('coordinatesUpdated', [{ 
                    latitude: lat.toFixed(6), 
                    longitude: lng.toFixed(6) 
                }]);
                
                // Fokus ke marker
                editMap.setView([lat, lng], editMap.getZoom());
            }

            // Pastikan peta di-render dengan benar
            setTimeout(() => {
                editMap.invalidateSize();
                editMapInitialized = true;
                console.log('Edit map initialized successfully');
            }, 300);

        } catch (e) {
            console.error('Error initializing map:', e);
            editMapInitialized = false;
        }
    }

    // Tambahkan event listener untuk modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modal_edit_lokasi');
        
        // Ketika modal dibuka
        modal.addEventListener('shown', function() {
            if (editMap) {
                setTimeout(() => {
                    editMap.invalidateSize();
                    console.log('Map resized after modal open');
                }, 100);
            }
        });

        // Ketika modal ditutup
        modal.addEventListener('close', function() {
            // Reset peta jika perlu
            editMapInitialized = false;
        });
    });

    // Livewire listeners
    document.addEventListener('livewire:init', () => {
        Livewire.on('openEditModal', (data) => {
            // Tampilkan modal
            modal_edit_lokasi.showModal();
            
            // Tunggu modal benar-benar terbuka sebelum inisialisasi peta
            setTimeout(() => {
                const locationData = data[0];
                if (locationData) {
                    initEditMap(
                        parseFloat(locationData.latitude) || -6.200000,
                        parseFloat(locationData.longitude) || 106.816666
                    );
                }
            }, 500);
        });
        
        // Event untuk update koordinat dari Livewire
        Livewire.on('coordinatesUpdated', (data) => {
            if (editMarker && data[0]) {
                const lat = parseFloat(data[0].latitude);
                const lng = parseFloat(data[0].longitude);
                editMarker.setLatLng([lat, lng]);
                editMap.setView([lat, lng], editMap.getZoom());
            }
        });
        
        Livewire.on('closeEditModal', () => {
            modal_edit_lokasi.close();
        });
    });
</script>