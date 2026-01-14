<div>
    <button class="btn btn-primary btn-sm gap-2"
        onclick="modal_create_lokasi.showModal(); setTimeout(initCreateMap, 200);">
        <x-heroicon-o-plus class="w-5 h-5" />
        Tambah Lokasi
    </button>

    <dialog id="modal_create_lokasi" class="modal" wire:ignore.self>
        <div class="modal-box max-w-3xl border border-base-300">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="modal_create_lokasi.close()">✕</button>
            </form>
            <h3 class="text-lg font-bold">Tambah Lokasi</h3>

            <form wire:submit.prevent="save">
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

                    <!-- Latitude -->
                    <fieldset>
                        <legend class="fieldset-legend">LATITUDE</legend>
                        <label
                            class="input w-full input-bordered flex items-center gap-2 @error('latitude') input-error @enderror">
                            <x-heroicon-o-map class="w-4 h-4 opacity-70" />
                            <input required type="text" id="create_latitude"
                                placeholder="-6.200000" readonly />
                        </label>
                        <input type="hidden" wire:model.defer="latitude" id="hidden_create_latitude" />
                        <p class="validator-hint hidden">Pilih lokasi di peta</p>
                        @error('latitude')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <!-- Longitude -->
                    <fieldset>
                        <legend class="fieldset-legend">LONGITUDE</legend>
                        <label
                            class="input w-full input-bordered flex items-center gap-2 @error('longitude') input-error @enderror">
                            <x-heroicon-o-map class="w-4 h-4 opacity-70" />
                            <input required type="text" id="create_longitude"
                                placeholder="106.816666" readonly />
                        </label>
                        <input type="hidden" wire:model.defer="longitude" id="hidden_create_longitude" />
                        <p class="validator-hint hidden">Pilih lokasi di peta</p>
                        @error('longitude')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <!-- Map - Full Width -->
                    <fieldset class="col-span-2">
                        <legend class="fieldset-legend">PILIH LOKASI DI PETA (KLIK UNTUK MENANDAI)</legend>
                        <div id="create_map" class="w-full h-56 rounded-lg border border-base-300 relative z-0"
                            wire:ignore></div>
                        <p class="text-xs text-gray-500 mt-2">Klik pada peta untuk menentukan lokasi</p>
                    </fieldset>

                    <!-- Status -->
                    <fieldset>
                        <legend class="fieldset-legend">STATUS</legend>
                        <label class="cursor-pointer label justify-start gap-3">
                            <input type="checkbox" name="status"
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
                            onclick="modal_create_lokasi.close()">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Simpan</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </dialog>
    
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Departemen berhasil ditambahkan!</span>
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
    let createMap = null;
    let createMarker = null;

    window.initCreateMap = function() {
        if (typeof L === 'undefined') {
            console.error('Leaflet not loaded');
            return;
        }

        if (createMap) {
            createMap.remove();
            createMap = null;
        }

        try {
            createMap = L.map('create_map').setView([0, 0], 2);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(createMap);

            createMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                if (createMarker) {
                    createMap.removeLayer(createMarker);
                }

                createMarker = L.marker([lat, lng]).addTo(createMap);

                // Update visible inputs
                document.getElementById('create_latitude').value = lat.toFixed(6);
                document.getElementById('create_longitude').value = lng.toFixed(6);
                
                // Update hidden inputs for Livewire
                document.getElementById('hidden_create_latitude').value = lat.toFixed(6);
                document.getElementById('hidden_create_longitude').value = lng.toFixed(6);
                
                // Trigger Livewire update
                document.getElementById('hidden_create_latitude').dispatchEvent(new Event('input'));
                document.getElementById('hidden_create_longitude').dispatchEvent(new Event('input'));

                createMap.setView([lat, lng], 15);
            });

            setTimeout(() => {
                createMap.invalidateSize();
            }, 100);
        } catch (e) {
            console.error('Error initializing map:', e);
        }
    }
    
    // Listen for close modal event
    document.addEventListener('livewire:init', () => {
        Livewire.on('closeCreateModal', () => {
            modal_create_lokasi.close();
            if (createMarker && createMap) {
                createMap.removeLayer(createMarker);
                createMarker = null;
            }
        });
    });
</script>
