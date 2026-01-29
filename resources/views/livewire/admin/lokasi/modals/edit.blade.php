<div x-data="modal('modal_edit_lokasi')">
    @teleport('body')
        <dialog id="modal_edit_lokasi" class="modal" wire:ignore.self>
            <div class="modal-box max-w-3xl border border-base-300">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
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
                                <input required type="text" wire:model.defer="nama_lokasi"
                                    placeholder="Contoh: Kantor Pusat" maxlength="100" />
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
                                <input required type="text" id="edit_latitude" placeholder="-6.200000" />
                            </label>
                            <!-- Hidden input untuk Livewire -->
                            <input type="hidden" wire:model.defer="latitude" id="hidden_edit_latitude" />
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
                                <input required type="text" id="edit_longitude" placeholder="106.816666" />
                            </label>
                            <!-- Hidden input untuk Livewire -->
                            <input type="hidden" wire:model.defer="longitude" id="hidden_edit_longitude" />
                            @error('longitude')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Map - Full Width -->
                        <fieldset class="col-span-2">
                            <legend class="fieldset-legend">PILIH LOKASI DI PETA (KLIK UNTUK MENANDAI)</legend>
                            <div class="mb-2">
                                <!-- Search Box -->
                                <div class="join w-full">
                                    <input type="text" id="edit_map_search" placeholder="Cari alamat atau tempat..."
                                        class="input input-bordered join-item w-full" />
                                    <button type="button" id="edit_map_search_btn" class="btn btn-primary join-item">
                                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                    </button>
                                </div>
                                <div id="edit_search_results" class="mt-2 hidden">
                                    <!-- Hasil pencarian akan muncul di sini -->
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gunakan pencarian atau klik peta untuk menentukan lokasi</p>
                            </div>
                            <div id="edit_map" class="w-full h-56 rounded-lg border border-base-300 relative z-0" wire:ignore></div>
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
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Update</span>
                                <span wire:loading>Mengupdate...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </dialog>
    @endteleport

    <x-partials.toast :success="$showSuccess ? 'Lokasi berhasil diupdate!' : null" :error="$showError ? ($errorMessage ?: 'Gagal menyimpan data!') : null" />
</div>

<script>
    let editMap = null;
    let editMarker = null;
    let editSearchTimeout = null;
    let editLastSearchQuery = '';
    let isEditUpdatingFromSearch = false;

    // Function to update Livewire hidden inputs
    function updateEditLivewireCoordinates(lat, lng) {
        if (isEditUpdatingFromSearch) return;

        const latInput = document.getElementById('hidden_edit_latitude');
        const lngInput = document.getElementById('hidden_edit_longitude');

        if (latInput && lngInput) {
            latInput.value = lat;
            lngInput.value = lng;

            // Trigger Livewire update - PENTING untuk deteksi perubahan
            latInput.dispatchEvent(new Event('input', { bubbles: true }));
            lngInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    // Function to update map from coordinates
    function updateEditMapFromCoordinates(lat, lng) {
        if (!editMap) return;

        // Validate coordinates
        if (!lat || !lng || isNaN(lat) || isNaN(lng)) return;

        lat = parseFloat(lat);
        lng = parseFloat(lng);

        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return;

        // Remove existing marker
        if (editMarker) {
            editMap.removeLayer(editMarker);
        }

        // Add new marker (draggable)
        editMarker = L.marker([lat, lng], {
            draggable: true
        }).addTo(editMap);

        // Event untuk drag marker
        editMarker.on('dragend', function(e) {
            const position = editMarker.getLatLng();
            updateEditCoordinates(position.lat, position.lng);
        });

        // Center map on marker
        editMap.setView([lat, lng], 15);

        // Update visible inputs
        document.getElementById('edit_latitude').value = lat.toFixed(6);
        document.getElementById('edit_longitude').value = lng.toFixed(6);

        // Update Livewire hidden inputs
        updateEditLivewireCoordinates(lat.toFixed(6), lng.toFixed(6));
    }

    // Function to update coordinates (digunakan saat klik map atau drag marker)
    function updateEditCoordinates(lat, lng) {
        // Update visible inputs
        document.getElementById('edit_latitude').value = lat.toFixed(6);
        document.getElementById('edit_longitude').value = lng.toFixed(6);

        // Update Livewire hidden inputs
        updateEditLivewireCoordinates(lat.toFixed(6), lng.toFixed(6));

        // Update marker position
        if (editMarker) {
            editMarker.setLatLng([lat, lng]);
        }
    }

    // Function to search location using OpenStreetMap Nominatim
    function searchEditLocation(query) {
        if (!query.trim() || query === editLastSearchQuery) return;

        editLastSearchQuery = query;

        const resultsContainer = document.getElementById('edit_search_results');
        if (resultsContainer) {
            resultsContainer.innerHTML =
                '<div class="text-center py-2"><span class="loading loading-spinner loading-sm"></span> Mencari...</div>';
            resultsContainer.classList.remove('hidden');
        }

        // Rate limiting: 1 detik delay antar request
        if (editSearchTimeout) {
            clearTimeout(editSearchTimeout);
        }

        editSearchTimeout = setTimeout(() => {
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
                                    <div class="p-3 border-b border-base-200 hover:bg-base-200 cursor-pointer edit-search-result-item" 
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
                            document.querySelectorAll('.edit-search-result-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    const lat = this.getAttribute('data-lat');
                                    const lng = this.getAttribute('data-lng');
                                    const name = this.getAttribute('data-name');

                                    // Tandai bahwa ini update dari search (untuk mencegah loop)
                                    isEditUpdatingFromSearch = true;

                                    // Update map
                                    updateEditMapFromCoordinates(lat, lng);

                                    // Update search input dengan nama lokasi
                                    document.getElementById('edit_map_search').value = name;

                                    // Sembunyikan hasil pencarian
                                    resultsContainer.classList.add('hidden');

                                    // Reset flag setelah delay
                                    setTimeout(() => {
                                        isEditUpdatingFromSearch = false;
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
                tap: false,
                dragging: true,
                scrollWheelZoom: true
            }).setView([lat, lng], 15);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19,
                detectRetina: true
            }).addTo(editMap);

            // Tambahkan marker awal (draggable)
            editMarker = L.marker([lat, lng], {
                draggable: true
            }).addTo(editMap);

            // Event untuk drag marker
            editMarker.on('dragend', function(e) {
                const position = editMarker.getLatLng();
                updateEditCoordinates(position.lat, position.lng);
            });

            // Event untuk klik peta
            editMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Update marker dan koordinat
                updateEditMapFromCoordinates(lat, lng);

                // Clear search results
                const resultsContainer = document.getElementById('edit_search_results');
                if (resultsContainer) {
                    resultsContainer.classList.add('hidden');
                }
            });

            // Initialize search functionality
            const searchInput = document.getElementById('edit_map_search');
            const searchBtn = document.getElementById('edit_map_search_btn');
            const resultsContainer = document.getElementById('edit_search_results');

            if (searchInput) {
                // Real-time search dengan debouncing
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.trim();
                    if (query.length >= 3) {
                        searchEditLocation(query);
                    } else if (resultsContainer) {
                        resultsContainer.classList.add('hidden');
                    }
                });

                // Clear search when clicking outside
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
                        searchEditLocation(query);
                    }
                });
            }

            // Monitor manual coordinate input changes
            const latInput = document.getElementById('edit_latitude');
            const lngInput = document.getElementById('edit_longitude');

            function handleEditManualCoordinateChange() {
                const lat = latInput.value;
                const lng = lngInput.value;

                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    const latNum = parseFloat(lat);
                    const lngNum = parseFloat(lng);

                    if (latNum >= -90 && latNum <= 90 && lngNum >= -180 && lngNum <= 180) {
                        updateEditMapFromCoordinates(latNum, lngNum);

                        if (resultsContainer) {
                            resultsContainer.classList.add('hidden');
                        }
                    }
                }
            }

            if (latInput && lngInput) {
                latInput.addEventListener('blur', handleEditManualCoordinateChange);
                lngInput.addEventListener('blur', handleEditManualCoordinateChange);

                latInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleEditManualCoordinateChange();
                    }
                });

                lngInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleEditManualCoordinateChange();
                    }
                });
            }

            // Update visible inputs dengan nilai awal
            document.getElementById('edit_latitude').value = lat.toFixed(6);
            document.getElementById('edit_longitude').value = lng.toFixed(6);

            // Update hidden inputs untuk Livewire
            updateEditLivewireCoordinates(lat.toFixed(6), lng.toFixed(6));

            // Pastikan peta di-render dengan benar
            setTimeout(() => {
                editMap.invalidateSize();
                console.log('Edit map initialized successfully');
            }, 300);

        } catch (e) {
            console.error('Error initializing map:', e);
        }
    }

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

        // Close modal event
        Livewire.on('closeEditModal', () => {
            modal_edit_lokasi.close();
            if (editMarker && editMap) {
                editMap.removeLayer(editMarker);
                editMarker = null;
            }

            // Clear search results
            const resultsContainer = document.getElementById('edit_search_results');
            if (resultsContainer) {
                resultsContainer.classList.add('hidden');
            }
        });
    });
</script>
