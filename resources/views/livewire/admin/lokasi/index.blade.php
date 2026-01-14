<div>
    <!-- Main Card -->
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <!-- Top Section: Search & Actions -->
            <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                <!-- Left: Search & Filter -->
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <!-- Search Input -->
                    <div class="form-control">
                        <label class="input input-sm">
                            <x-bi-search class="w-3" />
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Cari lokasi..." />
                        </label>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5" />
                            Filter
                            @if ($filterStatus)
                                <span class="badge badge-primary badge-sm">1</span>
                            @endif
                        </label>
                        <div tabindex="0"
                            class="dropdown-content z-10 card card-compact w-64 p-4 bg-base-100 border border-base-300 mt-2">
                            <div class="space-y-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Status</span>
                                    </label>
                                    <select wire:model.live="filterStatus" class="select select-bordered select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Non-Aktif</option>
                                    </select>
                                </div>
                                <button wire:click="resetFilters" class="btn btn-ghost btn-sm w-full">
                                    Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Create Button -->
                <livewire:admin.lokasi.modals.create />
            </div>

            <!-- Table Section -->
            @php
                $columns = [
                    ['label' => 'No', 'class' => 'w-16'],
                    ['label' => 'Nama Lokasi', 'field' => 'nama_lokasi', 'sortable' => true],
                    ['label' => 'Koordinat', 'class' => 'w-64'],
                    ['label' => 'Radius', 'field' => 'radius_meter', 'sortable' => true],
                    ['label' => 'Status'],
                    ['label' => 'Aksi', 'class' => 'text-center w-20'],
                ];
            @endphp

            <x-partials.table :columns="$columns" :data="$lokasis" :sortField="$sortField" :sortDirection="$sortDirection"
                emptyMessage="Tidak ada data lokasi" emptyIcon="heroicon-o-map-pin">
                @foreach ($lokasis as $index => $lokasi)
                    <tr wire:key="lokasi-{{ $lokasi->id }}" class="hover:bg-base-200 transition-colors duration-150">
                        <td>{{ $lokasis->firstItem() + $index }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="bg-primary/20 p-2 rounded-lg">
                                    <x-heroicon-o-map-pin class="w-4 h-4 text-primary" />
                                </div>
                                <span class="font-semibold">{{ $lokasi->nama_lokasi }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col text-xs font-mono">
                                <span class="text-gray-600">Lat: {{ number_format($lokasi->latitude, 6) }}</span>
                                <span class="text-gray-600">Long: {{ number_format($lokasi->longitude, 6) }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info badge-soft badge-sm">{{ $lokasi->radius_meter }} meter</span>
                        </td>
                        <td>
                            <span
                                class="badge badge-sm badge-soft
                                {{ $lokasi->status === 'active' ? 'badge-success' : 'badge-error' }}">
                                {{ ucfirst($lokasi->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
                                    <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                </label>
                                <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 border border-base-300 z-50">
                                    <li>
                                        <button onclick="viewMapModal{{ $lokasi->id }}.showModal()" class="flex items-center gap-2">
                                            <x-heroicon-o-map class="w-4 h-4" />
                                            <span>Lihat Peta</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button wire:click="$dispatch('edit-lokasi', {id: {{ $lokasi->id }}})" class="flex items-center gap-2">
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                            <span>Edit</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button wire:click="$dispatch('delete-lokasi', {id: {{ $lokasi->id }}})" class="flex items-center gap-2 text-error hover:bg-error hover:text-error-content">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                            <span>Hapus</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    <!-- Map Modal for each location -->
                    <dialog id="viewMapModal{{ $lokasi->id }}" class="modal">
                        <div class="modal-box w-11/12 max-w-3xl">
                            <form method="dialog">
                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                            </form>
                            <h3 class="font-bold text-lg mb-4">{{ $lokasi->nama_lokasi }}</h3>
                            <div id="map{{ $lokasi->id }}" class="w-full h-96 rounded-lg border border-base-300"></div>
                            <div class="mt-4 text-sm">
                                <p><strong>Koordinat:</strong> {{ number_format($lokasi->latitude, 6) }}, {{ number_format($lokasi->longitude, 6) }}</p>
                                <p><strong>Radius:</strong> {{ $lokasi->radius_meter }} meter</p>
                            </div>
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
                @endforeach
            </x-partials.table>

            <!-- Footer: Pagination -->
            <div class="mt-6 pt-4 border-t border-base-300">
                <x-partials.pagination :paginator="$lokasis" :perPage="$perPage" />
            </div>
        </div>
    </div>
    
    <!-- Modals -->
    <livewire:admin.lokasi.modals.edit />
    <livewire:admin.lokasi.modals.delete />
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($lokasis as $lokasi)
                // Initialize map when modal is opened
                const modal{{ $lokasi->id }} = document.getElementById('viewMapModal{{ $lokasi->id }}');
                if (modal{{ $lokasi->id }}) {
                    modal{{ $lokasi->id }}.addEventListener('show', function() {
                        setTimeout(() => {
                            const map = L.map('map{{ $lokasi->id }}').setView([{{ $lokasi->latitude }}, {{ $lokasi->longitude }}], 15);
                            
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors'
                            }).addTo(map);
    
                            // Add marker
                            const marker = L.marker([{{ $lokasi->latitude }}, {{ $lokasi->longitude }}]).addTo(map);
                            marker.bindPopup('<b>{{ $lokasi->nama_lokasi }}</b>').openPopup();
    
                            // Add radius circle
                            L.circle([{{ $lokasi->latitude }}, {{ $lokasi->longitude }}], {
                                color: 'blue',
                                fillColor: '#30f',
                                fillOpacity: 0.2,
                                radius: {{ $lokasi->radius_meter }}
                            }).addTo(map);
                        }, 100);
                    });
                }
            @endforeach
        });
    </script>
</div>
