<div class="space-y-4">
    <!-- Informasi Waktu Realtime -->
    <div class="card bg-gradient-to-r from-primary/10 to-primary/5 border border-primary/20">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-clock class="w-8 h-8 text-primary" />
                    <div>
                        <div class="text-sm text-base-content/70">Waktu Sekarang</div>
                        <div class="text-2xl font-bold text-primary"><span class="font-mono" x-data="{ time: '{{ now()->format('H:i:s') }}' }"
                                x-init="setInterval(() => {
                                    let date = new Date();
                                    let hours = String(date.getHours()).padStart(2, '0');
                                    let minutes = String(date.getMinutes()).padStart(2, '0');
                                    let seconds = String(date.getSeconds()).padStart(2, '0');
                                    time = `${hours}:${minutes}:${seconds}`;
                                }, 1000);" x-text="time"></span></div>
                        <div class="text-xs text-base-content/60">{{ $now->isoFormat('dddd, D MMMM YYYY') }}</div>
                    </div>
                </div>
                @if ($waktuBatasAbsenMasuk && !$absensiHariIni?->jam_masuk)
                    <div class="text-right">
                        <div class="text-xs text-base-content/70">Batas Absen Masuk</div>
                        <div class="text-lg font-bold text-warning">{{ $waktuBatasAbsenMasuk->format('H:i') }}</div>
                        @if ($now->greaterThan($waktuBatasAbsenMasuk))
                            <div class="badge badge-error badge-sm mt-1">Terlewat</div>
                        @else
                            <div class="badge badge-success badge-sm mt-1">Masih Tersedia</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Informasi Shift Hari Ini -->
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h2 class="card-title">
                <x-heroicon-o-calendar-days class="w-6 h-6" />
                Jadwal Shift Hari Ini
            </h2>
            @if ($jadwalHariIni)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="flex items-center gap-3">
                        <div class="badge badge-primary badge-lg">
                            <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                            {{ $jadwalHariIni->shift->nama_shift ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-arrow-right-circle class="w-5 h-5 text-success" />
                        <span class="font-semibold">Jam Masuk:</span>
                        <span>{{ $jadwalHariIni->shift->jam_masuk ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-arrow-left-circle class="w-5 h-5 text-error" />
                        <span class="font-semibold">Jam Pulang:</span>
                        <span>{{ $jadwalHariIni->shift->jam_pulang ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="mt-3 space-y-2">
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-information-circle class="w-4 h-4" />
                        <span>Toleransi Keterlambatan: {{ $jadwalHariIni->shift->toleransi_menit ?? 0 }} menit</span>
                    </div>
                    @if ($jadwalHariIni->lokasi)
                        <div class="flex items-center gap-2 text-sm">
                            <x-heroicon-o-map-pin class="w-4 h-4 text-info" />
                            <span>Lokasi: <strong>{{ $jadwalHariIni->lokasi->nama_lokasi }}</strong></span>
                        </div>
                    @endif
                </div>
            @else
                <div class="alert alert-warning mt-4">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    <span>Tidak ada jadwal shift untuk hari ini</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Maps Lokasi -->
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h2 class="card-title">
                <x-heroicon-o-map-pin class="w-6 h-6" />
                Lokasi Anda
            </h2>
            <div wire:ignore>
                <div id="map" class="w-full h-64 md:h-96 rounded-lg mt-4 relative z-0"
                    style="background: #e5e7eb;">
                </div>
                <div id="location-status" class="alert alert-info mt-4">
                    <x-heroicon-o-information-circle class="w-6 h-6" />
                    <span>Mengaktifkan lokasi...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Absen Masuk & Keluar -->
    @if ($jadwalHariIni)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" wire:poll.5s>
            @if (!$absensiHariIni || !$absensiHariIni->jam_masuk)
                <!-- Tombol Absen Masuk -->
                <div>
                    @if ($canAbsenMasuk)
                        <a href="{{ route('karyawan.absen.absensi') }}" class="btn btn-success btn-lg w-full gap-2">
                            <x-heroicon-o-arrow-right-circle class="w-6 h-6" />
                            Absen Masuk
                        </a>
                        <div class="alert alert-info mt-2 py-2">
                            <x-heroicon-o-information-circle class="w-5 h-5" />
                            <span class="text-sm">Batas waktu:
                                {{ $waktuBatasAbsenMasuk ? $waktuBatasAbsenMasuk->format('H:i') : '-' }}</span>
                        </div>
                    @else
                        <button class="btn btn-disabled btn-lg w-full gap-2" disabled>
                            <x-heroicon-o-arrow-right-circle class="w-6 h-6" />
                            Absen Masuk
                        </button>
                        @if ($absenMasukMessage)
                            @if (str_contains($absenMasukMessage, 'terlewat'))
                                <div class="alert alert-error mt-2 py-2">
                                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                                    <div class="text-sm">
                                        <div class="font-semibold">Terlambat!</div>
                                        <div>{{ $absenMasukMessage }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2 py-2">
                                    <x-heroicon-o-clock class="w-5 h-5" />
                                    <span class="text-sm">{{ $absenMasukMessage }}</span>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>

                <!-- Tombol Absen Keluar (Disabled) -->
                <div>
                    <button class="btn btn-disabled btn-lg w-full gap-2" disabled>
                        <x-heroicon-o-arrow-left-circle class="w-6 h-6" />
                        Absen Keluar
                    </button>
                    <div class="alert alert-info mt-2 py-2">
                        <x-heroicon-o-information-circle class="w-5 h-5" />
                        <span class="text-sm">Lakukan absen masuk terlebih dahulu</span>
                    </div>
                </div>
            @elseif($absensiHariIni && $absensiHariIni->jam_masuk && !$absensiHariIni->jam_pulang)
                <!-- Info Sudah Absen Masuk -->
                <div class="alert alert-success">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                    <div>
                        <div class="font-bold">Sudah Absen Masuk</div>
                        <div class="text-sm">Jam: {{ $absensiHariIni->jam_masuk }}</div>
                        @if ($absensiHariIni->status === 'terlambat')
                            <div class="badge badge-warning badge-sm mt-1">Terlambat</div>
                        @else
                            <div class="badge badge-success badge-sm mt-1">Tepat Waktu</div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Absen Keluar -->
                <div>
                    @if ($canAbsenPulang)
                        <a href="{{ route('karyawan.absen.absensi') }}?type=pulang"
                            class="btn btn-error btn-lg w-full gap-2">
                            <x-heroicon-o-arrow-left-circle class="w-6 h-6" />
                            Absen Keluar
                        </a>
                        <div class="alert alert-success mt-2 py-2">
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                            <span class="text-sm">Sudah waktunya absen keluar</span>
                        </div>
                    @else
                        <button class="btn btn-disabled btn-lg w-full gap-2" disabled>
                            <x-heroicon-o-arrow-left-circle class="w-6 h-6" />
                            Absen Keluar
                        </button>
                        @if ($absenPulangMessage)
                            <div class="alert alert-warning mt-2 py-2">
                                <x-heroicon-o-clock class="w-5 h-5" />
                                <span class="text-sm">{{ $absenPulangMessage }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <!-- Info Sudah Absen Lengkap -->
                <div class="alert alert-success col-span-2">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                    <div>
                        <div class="font-bold">Absensi Hari Ini Sudah Lengkap</div>
                        <div class="text-sm">Masuk: {{ $absensiHariIni->jam_masuk }} | Pulang:
                            {{ $absensiHariIni->jam_pulang }}</div>
                        <div class="flex gap-2 mt-2">
                            @if ($absensiHariIni->status === 'terlambat')
                                <div class="badge badge-warning badge-sm">Terlambat</div>
                            @else
                                <div class="badge badge-success badge-sm">Tepat Waktu</div>
                            @endif
                            <div class="badge badge-info badge-sm">{{ $absensiHariIni->lokasi->nama_lokasi ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Tidak Ada Jadwal -->
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <div class="alert alert-error">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    <div>
                        <div class="font-bold text-sm">Tidak Ada Jadwal Shift</div>
                        <div class="text-xs">Tidak ada jadwal shift untuk hari ini. Silakan hubungi admin untuk
                            informasi lebih lanjut.</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabel 5 Absensi Terbaru -->
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h2 class="card-title">
                <x-heroicon-o-clock class="w-6 h-6" />
                Riwayat Absensi Terbaru
            </h2>

            <x-partials.table :columns="[
                ['label' => 'Tanggal', 'field' => 'tanggal', 'sortable' => false],
                ['label' => 'Jam Masuk', 'field' => 'jam_masuk', 'sortable' => false],
                ['label' => 'Jam Pulang', 'field' => 'jam_pulang', 'sortable' => false],
                ['label' => 'Lokasi', 'field' => 'lokasi', 'sortable' => false],
                ['label' => 'Status', 'field' => 'status', 'sortable' => false],
                ['label' => 'Aksi', 'field' => 'actions', 'sortable' => false, 'class' => 'text-center'],
            ]" :data="$riwayatAbsensi" emptyMessage="Belum ada riwayat absensi"
                emptyIcon="heroicon-o-clock">
                @foreach ($riwayatAbsensi as $absensi)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y') }}</td>
                        <td>
                            @if ($absensi->jam_masuk)
                                <span class="badge badge-success">{{ $absensi->jam_masuk }}</span>
                            @else
                                <span class="badge badge-ghost">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($absensi->jam_pulang)
                                <span class="badge badge-error">{{ $absensi->jam_pulang }}</span>
                            @else
                                <span class="badge badge-ghost">-</span>
                            @endif
                        </td>
                        <td>{{ $absensi->lokasi->nama_lokasi ?? 'N/A' }}</td>
                        <td>
                            @if ($absensi->status === 'hadir')
                                <span class="badge badge-success">Hadir</span>
                            @elseif($absensi->status === 'terlambat')
                                <span class="badge badge-warning">Terlambat</span>
                            @elseif($absensi->status === 'izin')
                                <span class="badge badge-info">Izin</span>
                            @else
                                <span class="badge badge-ghost">{{ ucfirst($absensi->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button wire:click="viewDetail({{ $absensi->id }})"
                                class="btn btn-ghost btn-sm btn-square">
                                <x-heroicon-o-eye class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                @endforeach
            </x-partials.table>
        </div>
    </div>

    <script>
        // Inisialisasi map hanya sekali
        (function() {
            // Cek apakah map sudah diinisialisasi
            if (window.mapInitialized) {
                return;
            }
            window.mapInitialized = true;

            const locationStatus = document.getElementById('location-status');
            let map = null;
            let userMarker = null;
            let officeMarker = null;
            let officeCircle = null;
            let distanceLine = null;

            // Data lokasi kantor dari server
            const officeLocation = @json($lokasiKantor);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;

                        // Initialize map (pastikan container kosong)
                        const mapContainer = document.getElementById('map');
                        if (mapContainer._leaflet_id) {
                            // Map sudah ada, skip
                            return;
                        }

                        // Tentukan center map
                        let centerLat = userLat;
                        let centerLng = userLng;
                        let zoomLevel = 15;

                        // Jika ada lokasi kantor, set center di tengah antara user dan kantor
                        if (officeLocation) {
                            centerLat = (userLat + parseFloat(officeLocation.latitude)) / 2;
                            centerLng = (userLng + parseFloat(officeLocation.longitude)) / 2;
                            zoomLevel = 14;
                        }

                        map = L.map('map').setView([centerLat, centerLng], zoomLevel);

                        // Add tile layer
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                            maxZoom: 19,
                        }).addTo(map);

                        // Custom icon untuk user (biru)
                        const userIcon = L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        });

                        // Custom icon untuk kantor (merah)
                        const officeIcon = L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        });

                        // Add user marker (biru)
                        userMarker = L.marker([userLat, userLng], {icon: userIcon}).addTo(map)
                            .bindPopup('<strong>📍 Lokasi Anda</strong>')
                            .openPopup();

                        // Add office marker dan circle jika ada data
                        if (officeLocation) {
                            const officeLat = parseFloat(officeLocation.latitude);
                            const officeLng = parseFloat(officeLocation.longitude);
                            const officeRadius = parseFloat(officeLocation.radius);

                            // Marker kantor (merah)
                            officeMarker = L.marker([officeLat, officeLng], {icon: officeIcon}).addTo(map)
                                .bindPopup(`<strong>🏢 ${officeLocation.nama}</strong><br>Radius: ${officeRadius}m`);

                            // Circle radius kantor (hijau transparan)
                            officeCircle = L.circle([officeLat, officeLng], {
                                color: '#10b981',      // hijau
                                fillColor: '#10b981',
                                fillOpacity: 0.15,
                                radius: officeRadius   // dalam meter
                            }).addTo(map);

                            // Hitung jarak antara user dan kantor (Haversine)
                            const distance = calculateDistance(userLat, userLng, officeLat, officeLng);

                            // Garis penghubung antara user dan kantor
                            distanceLine = L.polyline([[userLat, userLng], [officeLat, officeLng]], {
                                color: '#3b82f6',
                                weight: 2,
                                opacity: 0.6,
                                dashArray: '5, 10'
                            }).addTo(map);

                            // Popup di tengah garis untuk menampilkan jarak
                            const midLat = (userLat + officeLat) / 2;
                            const midLng = (userLng + officeLng) / 2;
                            L.popup()
                                .setLatLng([midLat, midLng])
                                .setContent(`<strong>Jarak: ${distance.toFixed(0)}m</strong>`)
                                .openOn(map);

                            // Fit bounds untuk menampilkan semua marker
                            const bounds = L.latLngBounds([
                                [userLat, userLng],
                                [officeLat, officeLng]
                            ]);
                            map.fitBounds(bounds, {padding: [50, 50]});
                        }

                        // Update status
                        let statusMessage = 'Lokasi berhasil diaktifkan';
                        if (officeLocation) {
                            const distance = calculateDistance(userLat, userLng, parseFloat(officeLocation.latitude), parseFloat(officeLocation.longitude));
                            const isInRadius = distance <= parseFloat(officeLocation.radius);
                            
                            if (isInRadius) {
                                locationStatus.className = 'alert alert-success mt-4';
                                statusMessage = `✅ Anda berada dalam radius kantor (${distance.toFixed(0)}m dari ${officeLocation.nama})`;
                            } else {
                                locationStatus.className = 'alert alert-warning mt-4';
                                statusMessage = `⚠️ Anda berada di luar radius kantor (${distance.toFixed(0)}m dari ${officeLocation.nama}, maks: ${officeLocation.radius}m)`;
                            }
                        } else {
                            locationStatus.className = 'alert alert-success mt-4';
                        }

                        locationStatus.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>${statusMessage}</span>
                        `;

                        // Watch position for live updates
                        navigator.geolocation.watchPosition(
                            function(position) {
                                const newLat = position.coords.latitude;
                                const newLng = position.coords.longitude;

                                if (userMarker) {
                                    userMarker.setLatLng([newLat, newLng]);
                                }

                                // Update distance line jika ada kantor
                                if (officeLocation && distanceLine) {
                                    const officeLat = parseFloat(officeLocation.latitude);
                                    const officeLng = parseFloat(officeLocation.longitude);
                                    distanceLine.setLatLngs([[newLat, newLng], [officeLat, officeLng]]);

                                    // Update jarak
                                    const distance = calculateDistance(newLat, newLng, officeLat, officeLng);
                                    const isInRadius = distance <= parseFloat(officeLocation.radius);

                                    // Update status message
                                    if (isInRadius) {
                                        locationStatus.className = 'alert alert-success mt-4';
                                        locationStatus.innerHTML = `
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>✅ Anda berada dalam radius kantor (${distance.toFixed(0)}m dari ${officeLocation.nama})</span>
                                    `;
                                    } else {
                                        locationStatus.className = 'alert alert-warning mt-4';
                                        locationStatus.innerHTML = `
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                        <span>⚠️ Anda berada di luar radius kantor (${distance.toFixed(0)}m dari ${officeLocation.nama}, maks: ${officeLocation.radius}m)</span>
                                    `;
                                    }
                                }
                            },
                            function(error) {
                                console.error('Error watching position:', error);
                            }, {
                                enableHighAccuracy: true,
                                maximumAge: 10000,
                                timeout: 5000
                            }
                        );
                    },
                    function(error) {
                        locationStatus.className = 'alert alert-error mt-4';
                        locationStatus.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <span>Gagal mengaktifkan lokasi. Silakan izinkan akses lokasi.</span>
                        `;

                        // Hide map if location is denied
                        document.getElementById('map').style.display = 'none';
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                locationStatus.className = 'alert alert-error mt-4';
                locationStatus.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <span>Browser Anda tidak mendukung geolocation.</span>
                `;
                document.getElementById('map').style.display = 'none';
            }

            // Fungsi Haversine untuk menghitung jarak
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371e3; // Radius bumi dalam meter
                const φ1 = lat1 * Math.PI / 180;
                const φ2 = lat2 * Math.PI / 180;
                const Δφ = (lat2 - lat1) * Math.PI / 180;
                const Δλ = (lon2 - lon1) * Math.PI / 180;

                const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return R * c; // Jarak dalam meter
            }
        })();
    </script>
</div>
