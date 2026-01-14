<ul class="menu menu-xs menu-horizontal bg-base-100 sidebar-menu w-full flex gap-2 fixed bottom-0 left-0 right-0 z-50 border-t border-base-300 shadow-lg">

    <li class="flex-1">
        <a wire:navigate href="{{ route('karyawan.dashboard.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('karyawan.dashboard.*') ? 'bg-base-300' : '' }}">
            <x-heroicon-o-home class="w-5" />
            <span class="text-xs">Beranda</span>
        </a>
    </li>
    <li class="flex-1">
        <a wire:navigate href="{{ route('karyawan.absen.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('karyawan.absen.*') ? 'bg-base-300' : '' }}">
            <x-heroicon-o-finger-print class="w-5" />
            <span class="text-xs">Absen</span>
        </a>
    </li>
    <li class="flex-1">
        <a wire:navigate href="{{ route('karyawan.jadwal.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('karyawan.jadwal.*') ? 'bg-base-300' : '' }}">
            <x-heroicon-o-calendar-days class="w-5" />
            <span class="text-xs">Jadwal</span>
        </a>
    </li>
    <li class="flex-1">
        <a wire:navigate href="{{ route('karyawan.menu.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('karyawan.menu.*') ? 'bg-base-300' : '' }}">
            <x-heroicon-o-squares-2x2 class="w-5" />
            <span class="text-xs">Menu</span>
        </a>
    </li>
    <li class="flex-1">
        <a wire:navigate href="{{ route('karyawan.profile.index') }}"
            class="flex flex-col items-center {{ request()->routeIs('karyawan.profile.*') ? 'bg-base-300' : '' }}">
            <x-heroicon-o-user-circle class="w-5" />
            <span class="text-xs">Profile</span>
        </a>
    </li>
</ul>
