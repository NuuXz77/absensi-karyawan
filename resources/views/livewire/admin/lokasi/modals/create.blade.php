<div x-data="modal('modal_create_lokasi')">
    <button class="btn btn-primary btn-sm gap-2" @click="openModal(); initCreateMap();">
        <x-heroicon-o-plus class="w-5 h-5" />
        Tambah Lokasi
    </button>

    @teleport('body')
        <dialog id="modal_create_lokasi" class="modal" wire:ignore.self x-show="open">
            <div class="modal-box max-w-3xl border border-base-300">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
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
                                <input required type="text" wire:model="nama_lokasi" placeholder="Contoh: Kantor Pusat"
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
                                <input required type="number" wire:model="radius_meter" placeholder="Contoh: 100"
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
                                <input required type="text" id="create_latitude" placeholder="-6.200000" />
                            </label>
                            <!-- Hidden input untuk Livewire -->
                            <input type="hidden" wire:model="latitude" id="hidden_create_latitude" />
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
                                <input required type="text" id="create_longitude" placeholder="106.816666" />
                            </label>
                            <!-- Hidden input untuk Livewire -->
                            <input type="hidden" wire:model="longitude" id="hidden_create_longitude" />
                            <p class="validator-hint hidden">Pilih lokasi di peta</p>
                            @error('longitude')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Map - Full Width -->
                        <fieldset class="col-span-2">
                            <legend class="fieldset-legend">PILIH LOKASI DI PETA (KLIK UNTUK MENANDAI)</legend>
                            <div class="mb-2">
                                <!-- Search Box - TIDAK menggunakan Livewire -->
                                <div class="join w-full">
                                    <input type="text" id="create_map_search" placeholder="Cari alamat atau tempat..."
                                        class="input input-bordered join-item w-full" />
                                    <button type="button" id="create_map_search_btn" class="btn btn-primary join-item">
                                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                    </button>
                                </div>
                                <div id="create_search_results" class="mt-2 hidden">
                                    <!-- Hasil pencarian akan muncul di sini -->
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gunakan pencarian atau klik peta untuk menentukan
                                    lokasi</p>
                            </div>
                            <div id="create_map" class="w-full h-56 rounded-lg border border-base-300 relative z-0"
                                wire:ignore></div>
                        </fieldset>

                        <!-- Status -->
                        <fieldset>
                            <legend class="fieldset-legend">STATUS</legend>
                            <label class="cursor-pointer label justify-start gap-3">
                                <input type="checkbox"
                                    wire:change="$set('status', $event.target.checked ? 'active' : 'inactive')"
                                    {{ $status === 'active' ? 'checked' : '' }} class="toggle toggle-success" />
                                <span class="label-text flex items-center gap-2">
                                    <x-heroicon-o-check-circle class="w-5 h-5 text-success" />
                                    Status Aktif
                                </span>
                            </label>
                        </fieldset>
                        <!-- Form Actions -->
                        <div class="modal-action">
                            {{-- <button type="button" class="btn btn-ghost"
                                onclick="modal_create_lokasi.close()">Batal</button> --}}
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Simpan</span>
                                <span wire:loading>Menyimpan...</span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </dialog>
    @endteleport

    <x-partials.toast :success="$showSuccess ? 'Lokasi berhasil ditambahkan!' : null" :error="$showError ? ($errorMessage ?: 'Gagal menyimpan data!') : null" />
</div>

<script>
    let createMap = null;
    let createMarker = null;
    let searchTimeout = null;
    let lastSearchQuery = '';
    let isUpdatingFromSearch = false;

    // Function to update Livewire hidden inputs
    function updateLivewireCoordinates(lat, lng) {
        if (isUpdatingFromSearch) return;

        const latInput = document.getElementById('hidden_create_latitude');
        const lngInput = document.getElementById('hidden_create_longitude');

        if (latInput && lngInput) {
            latInput.value = lat;
            lngInput.value = lng;

            // Trigger Livewire update
            latInput.dispatchEvent(new Event('input'));
            lngInput.dispatchEvent(new Event('input'));
        }
    }

    // Function to update map from coordinates
    function updateMapFromCoordinates(lat, lng) {
        if (!createMap) return;

        // Validate coordinates
        if (!lat || !lng || isNaN(lat) || isNaN(lng)) return;

        lat = parseFloat(lat);
        lng = parseFloat(lng);

        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return;

        // Remove existing marker
        if (createMarker) {
            createMap.removeLayer(createMarker);
        }

        // Add new marker
        createMarker = L.marker([lat, lng]).addTo(createMap);

        // Center map on marker
        createMap.setView([lat, lng], 15);

        // Update visible inputs (tapi TIDAK trigger Livewire)
        document.getElementById('create_latitude').value = lat.toFixed(6);
        document.getElementById('create_longitude').value = lng.toFixed(6);

        // Update Livewire hidden inputs
        updateLivewireCoordinates(lat.toFixed(6), lng.toFixed(6));
    }

    // Function to search location using OpenStreetMap Nominatim
    function searchLocation(query) {
        if (!query.trim() || query === lastSearchQuery) return;

        lastSearchQuery = query;

        const resultsContainer = document.getElementById('create_search_results');
        if (resultsContainer) {
            resultsContainer.innerHTML =
                '<div class="text-center py-2"><span class="loading loading-spinner loading-sm"></span> Mencari...</div>';
            resultsContainer.classList.remove('hidden');
        }

        // Rate limiting: 1 detik delay antar request
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        searchTimeout = setTimeout(() => {
            const url =
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id&accept-language=id`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (resultsContainer) {
                        resultsContainer.classList.remove('hidden');

                        if (data && data.length > 0) {
                            let html =
                                '<div class="bg-base-100 border border-base-300 rounded-lg mt-1 max-h-48 overflow-y-auto">';
                            data.forEach((result, index) => {
                                const lat = parseFloat(result.lat);
                                const lng = parseFloat(result.lon);
                                const displayName = result.display_name.length > 100 ?
                                    result.display_name.substring(0, 100) + '...' :
                                    result.display_name;

                                html += `
                                    <div class="p-3 border-b border-base-200 hover:bg-base-200 cursor-pointer search-result-item" 
                                         data-lat="${lat}" 
                                         data-lng="${lng}"
                                         data-name="${result.display_name}">
                                        <div class="font-medium">${displayName}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            resultsContainer.innerHTML = html;

                            // Add click event to search results
                            document.querySelectorAll('.search-result-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    const lat = this.getAttribute('data-lat');
                                    const lng = this.getAttribute('data-lng');
                                    const name = this.getAttribute('data-name');

                                    // Tandai bahwa ini update dari search (untuk mencegah loop)
                                    isUpdatingFromSearch = true;

                                    // Update map
                                    updateMapFromCoordinates(lat, lng);

                                    // Update search input dengan nama lokasi
                                    document.getElementById('create_map_search').value =
                                        name;

                                    // Sembunyikan hasil pencarian
                                    resultsContainer.classList.add('hidden');

                                    // Reset flag setelah delay
                                    setTimeout(() => {
                                        isUpdatingFromSearch = false;
                                    }, 100);
                                });
                            });
                        } else {
                            resultsContainer.innerHTML =
                                '<div class="text-center py-2 text-gray-500">Tidak ada hasil ditemukan</div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    if (resultsContainer) {
                        resultsContainer.innerHTML =
                            '<div class="text-center py-2 text-error">Gagal mencari lokasi</div>';
                    }
                });
        }, 1000); // Delay 1 detik untuk rate limiting
    }

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
            // Default center (Jakarta)
            const defaultLat = -6.2088;
            const defaultLng = 106.8456;

            createMap = L.map('create_map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(createMap);

            // Map click event
            createMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Update map marker
                updateMapFromCoordinates(lat, lng);

                // Clear search results
                const resultsContainer = document.getElementById('create_search_results');
                if (resultsContainer) {
                    resultsContainer.classList.add('hidden');
                }
            });

            // Initialize search functionality
            const searchInput = document.getElementById('create_map_search');
            const searchBtn = document.getElementById('create_map_search_btn');
            const resultsContainer = document.getElementById('create_search_results');

            if (searchInput) {
                // Real-time search dengan debouncing
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.trim();
                    if (query.length >= 3) {
                        searchLocation(query);
                    } else if (resultsContainer) {
                        resultsContainer.classList.add('hidden');
                    }
                });

                // Clear search when clicking on map or other actions
                document.addEventListener('click', function(e) {
                    if (resultsContainer && !resultsContainer.contains(e.target) &&
                        searchInput && !searchInput.contains(e.target) &&
                        searchBtn && !searchBtn.contains(e.target)) {
                        resultsContainer.classList.add('hidden');
                    }
                });
            }

            if (searchBtn) {
                searchBtn.addEventListener('click', function() {
                    const query = searchInput.value;
                    if (query.trim().length >= 3) {
                        searchLocation(query);
                    }
                });
            }

            // Monitor manual coordinate input changes
            const latInput = document.getElementById('create_latitude');
            const lngInput = document.getElementById('create_longitude');

            function handleManualCoordinateChange() {
                const lat = latInput.value;
                const lng = lngInput.value;

                // Validasi sederhana
                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    const latNum = parseFloat(lat);
                    const lngNum = parseFloat(lng);

                    if (latNum >= -90 && latNum <= 90 && lngNum >= -180 && lngNum <= 180) {
                        // Update map
                        updateMapFromCoordinates(latNum, lngNum);

                        // Clear search results
                        if (resultsContainer) {
                            resultsContainer.classList.add('hidden');
                        }
                    }
                }
            }

            // Update map ketika manual input selesai (on blur)
            if (latInput && lngInput) {
                latInput.addEventListener('blur', handleManualCoordinateChange);
                lngInput.addEventListener('blur', handleManualCoordinateChange);

                // Juga update saat tekan Enter
                latInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleManualCoordinateChange();
                    }
                });

                lngInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleManualCoordinateChange();
                    }
                });
            }

            // Resize map after modal opens
            setTimeout(() => {
                if (createMap) {
                    createMap.invalidateSize();

                    // Check for existing coordinates in hidden inputs
                    const hiddenLat = document.getElementById('hidden_create_latitude');
                    const hiddenLng = document.getElementById('hidden_create_longitude');

                    if (hiddenLat && hiddenLng && hiddenLat.value && hiddenLng.value) {
                        // Update visible inputs
                        latInput.value = parseFloat(hiddenLat.value).toFixed(6);
                        lngInput.value = parseFloat(hiddenLng.value).toFixed(6);

                        // Update map
                        updateMapFromCoordinates(hiddenLat.value, hiddenLng.value);
                    }
                }
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

            // Clear search results
            const resultsContainer = document.getElementById('create_search_results');
            if (resultsContainer) {
                resultsContainer.classList.add('hidden');
            }
        });
    });
</script>
