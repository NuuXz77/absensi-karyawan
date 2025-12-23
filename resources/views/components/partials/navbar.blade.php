<nav class="navbar bg-base-300 border-b border-base-100 sticky top-0 z-50">
    <div class="flex-1 flex items-center gap-2">
        <!-- Mobile menu toggle button -->
        <label for="sidebar-drawer" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                class="inline-block h-5 w-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </label>
    </div>

    <div class="flex-none gap-2">
        {{-- <!-- Notifications -->
        <div class="dropdown dropdown-end">
            <button class="btn btn-ghost btn-circle">
                <div class="indicator">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="badge badge-xs badge-primary indicator-item"></span>
                </div>
            </button>
            <div class="dropdown-content bg-primary shadow-lg rounded-box z-50 w-80 mt-4 p-0">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-lg">Notifikasi</h3>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <div class="p-4 hover:bg-base-200">
                        <div class="flex items-start">
                            <div class="avatar placeholder mr-3">
                                <div class="bg-info text-info-content rounded-full w-10">
                                    <i class="fas fa-info"></i>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium">Sistem Update</p>
                                <p class="text-sm text-gray-500">Aplikasi telah diperbarui ke versi 2.0</p>
                                <p class="text-xs text-gray-400 mt-1">2 jam yang lalu</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t text-center">
                    <a href="#" class="link link-primary">Lihat semua notifikasi</a>
                </div>
            </div>
        </div>
         --}}
        <!-- User Profile Dropdown -->
        <div class="dropdown dropdown-end">
            <div class="flex items-center gap-3 cursor-pointer pr-4" tabindex="0">
                <div class="avatar avatar-online">
                    <button class="btn btn-primary btn-circle">
                        <div>
                            @if (Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="User Avatar" />
                            @else
                                <span
                                    class="text-lg font-bold">{{ strtoupper(implode('', array_map(fn($word) => substr($word, 0, 1), explode(' ', Auth::user()->username)))) }}</span>
                            @endif
                        </div>
                    </button>
                </div>
                <div class="hidden md:flex flex-col items-start">
                    <p class="font-semibold text-sm">{{ ucwords(Auth::user()->username) }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
            <ul class="dropdown-content menu bg-base-300 border border-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                <li>
                    <a href="/profile">
                        <x-heroicon-o-user-circle class="w-5 h-5" />
                        Profil Saya
                    </a>
                </li>
                <li>
                    <a href="/">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                        Pengaturan
                    </a>
                </li>
                <li>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-error">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                            Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
