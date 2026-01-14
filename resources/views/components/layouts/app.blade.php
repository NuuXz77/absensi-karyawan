<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Absensi App') }} - {{ $title ?? 'Dashboard' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Face-API JS -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen">
    @if (Auth::check() && Auth::user()->role === 'admin')
        {{-- ============================================ --}}
        {{-- LAYOUT UNTUK ADMIN (Drawer Sidebar di Kiri) --}}
        {{-- ============================================ --}}
        <div class="drawer lg:drawer-open">
            <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />

            <!-- Content -->
            <div class="drawer-content flex flex-col">
                <x-partials.navbar />

                <!-- Main Content -->
                <main class="flex-1 p-4 md:p-6 bg-base-200">
                    <div class="w-full mx-auto">
                        <!-- Page Header -->
                        @if (isset($header))
                            {{ $header }}
                        @else
                            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <h1 class="text-xl font-bold text-base-content">{{ $title ?? 'Dashboard' }}</h1>

                                <!-- Breadcrumbs -->
                                <div class="breadcrumbs text-sm">
                                    <ul>
                                        <li>
                                            <a wire:navigate href="{{ route('admin.dashboard.index') }}">
                                                <x-heroicon-o-home class="w-4 h-4" />
                                                Dashboard
                                            </a>
                                        </li>
                                        @php
                                            $segments = request()->segments();
                                            $url = '/' . ($segments[0] ?? '');
                                        @endphp
                                        @foreach ($segments as $key => $segment)
                                            @if ($key > 0)
                                                @php
                                                    $url .= '/' . $segment;
                                                    $isLast = $key === count($segments) - 1;
                                                    $label = ucfirst(str_replace('-', ' ', $segment));
                                                @endphp
                                                <li>
                                                    @if ($isLast)
                                                        <span class="font-medium">{{ $label }}</span>
                                                    @else
                                                        <a wire:navigate
                                                            href="{{ $url }}">{{ $label }}</a>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Page Content -->
                        {{ $slot }}
                    </div>
                </main>

                <!-- Footer -->
                <footer class="footer footer-center p-4 text-base-content bg-base-100 border-t border-base-300">
                    <aside>
                        <p>Copyright © {{ date('Y') }} - {{ config('app.name', 'Absensi App') }}. All rights
                            reserved.</p>
                    </aside>
                </footer>
            </div>

            {{-- Admin Sidebar --}}
            <x-partials.sidebar />
        </div>
    @else
        {{-- ================================================= --}}
        {{-- LAYOUT UNTUK KARYAWAN (Bottom Navigation di Bawah) --}}
        {{-- ================================================= --}}
        <div class="flex flex-col min-h-screen">
            <x-partials.karyawan-navbar />

            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-6 bg-base-200 pb-24">
                <div class="w-full mx-auto">
                    <!-- Page Header -->
                    @if (isset($header))
                        {{ $header }}
                    @else
                        <div class="mb-6">
                            <h1 class="text-xl font-bold text-base-content">{{ $title ?? 'Dashboard' }}</h1>
                        </div>
                    @endif

                    <!-- Page Content -->
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            {{-- <footer class="footer footer-center p-4 text-base-content bg-base-300 border-t border-base-100 mb-20">
                    <aside>
                        <p class="text-sm">Copyright © {{ date('Y') }} - {{ config('app.name', 'Absensi App') }}</p>
                    </aside>
                </footer> --}}

            {{-- Bottom Navigation Bar --}}
            <x-partials.karyawan-sidebar />
        </div>
    @endif

    @livewireScripts
    <script>
        // Active menu highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll('.sidebar-menu a');

            menuLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && currentPath.startsWith(href.replace(/\/$/, '')) && href !== '/') {
                    link.parentElement.classList.add('active');
                }
            });
        });

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const drawer = document.getElementById('sidebar-drawer');
            drawer.checked = !drawer.checked;
        }
    </script>
</body>

</html>
