<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Absensi App') }} - {{ $title ?? 'Dashboard' }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="min-h-screen">
        <div class="drawer lg:drawer-open">
            <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />

            <!-- Content -->
            <div class="drawer-content flex flex-col">
                <!-- Navbar -->
                <x-partials.navbar />

                <!-- Main Content -->
                <main class="flex-1 p-4 md:p-6 bg-base-200">
                    <div class="w-full mx-auto">
                        <!-- Page Header -->
                        @if(isset($header))
                            {{ $header }}
                        @else
                            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <h1 class="text-xl font-bold text-base-content">{{ $title ?? 'Dashboard' }}</h1>
                                
                                <!-- Breadcrumbs -->
                                <div class="breadcrumbs text-sm">
                                    <ul>
                                        <li>
                                            <a wire:navigate href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard.index') : route('dashboard') }}">
                                                <x-heroicon-o-home class="w-4 h-4" />
                                                Dashboard
                                            </a>
                                        </li>
                                        @php
                                            $segments = request()->segments();
                                            $url = '/' . ($segments[0] ?? ''); // Start with first segment (admin or role)
                                        @endphp
                                        @foreach($segments as $key => $segment)
                                            @if($key > 0) {{-- Skip first segment (admin/karyawan) --}}
                                                @php
                                                    $url .= '/' . $segment;
                                                    $isLast = $key === count($segments) - 1;
                                                    $label = ucfirst(str_replace('-', ' ', $segment));
                                                @endphp
                                                <li>
                                                    @if($isLast)
                                                        <span class="font-medium">{{ $label }}</span>
                                                    @else
                                                        <a wire:navigate href="{{ $url }}">{{ $label }}</a>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="alert alert-success shadow-lg mb-6">
                                <div>
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                    <span>{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info shadow-lg mb-6">
                                <div>
                                    <x-heroicon-o-information-circle class="w-5 h-5" />
                                    <span>{{ session('info') }}</span>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-error shadow-lg mb-6">
                                <div>
                                    <x-heroicon-o-exclamation-circle class="w-5 h-5" />
                                    <span>{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Page Content -->
                        {{ $slot }}
                    </div>
                </main>

                <!-- Footer -->
                <footer class="footer footer-center p-4 text-base-content bg-base-300 border-t border-base-100">
                    <aside>
                        <p>Copyright © {{ date('Y') }} - {{ config('app.name', 'Absensi App') }}. All rights
                            reserved.</p>
                    </aside>
                </footer>
            </div>

            <!-- Sidebar -->
            <x-partials.sidebar />
        </div>

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
