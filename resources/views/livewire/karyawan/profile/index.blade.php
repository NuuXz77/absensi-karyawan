{{-- 
    Profile Karyawan - Index Page
    
    Struktur Modal Terpisah:
    - Modals terpisah untuk kemudahan maintenance dan scalability
    - Setiap modal memiliki Livewire component sendiri
    - Layout responsif dengan grid system
    - Icon integration menggunakan Heroicons
    
    Modal Components:
    1. InformasiAkun     : resources/views/livewire/karyawan/profile/modals/informasi-akun.blade.php
    2. DataPribadi       : resources/views/livewire/karyawan/profile/modals/data-pribadi.blade.php
    3. FotoIdentitas     : resources/views/livewire/karyawan/profile/modals/foto-identitas.blade.php
    4. KeamananAkun      : resources/views/livewire/karyawan/profile/modals/keamanan-akun.blade.php
    5. InformasiSistem   : resources/views/livewire/karyawan/profile/modals/informasi-sistem.blade.php
--}}

<div class="space-y-4">
    {{-- Header Profile Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg p-8">
        {{-- Foto Profile --}}
        <div class="flex justify-center mb-4">
            <div class="avatar">
                <div class="w-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                    @if ($karyawan && $karyawan->foto_karyawan)
                        <img src="{{ asset('storage/' . $karyawan->foto_karyawan) }}" alt="Foto Profile" />
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ $karyawan ? urlencode($karyawan->nama_lengkap) : 'User' }}&background=random&size=128"
                            alt="Foto Profile" />
                    @endif
                </div>
            </div>
        </div>

        {{-- Nama --}}
        <div class="text-center mb-2">
            <h1 class="text-2xl font-bold text-base-content">
                {{ $karyawan ? $karyawan->nama_lengkap : 'Nama Tidak Tersedia' }}
            </h1>
        </div>

        {{-- Jabatan - Departemen --}}
        <div class="text-center">
            <p class="text-base-content/70 text-sm">
                {{ $karyawan && $karyawan->jabatan ? $karyawan->jabatan->nama_jabatan : 'Jabatan' }} -
                {{ $karyawan && $karyawan->departemen ? $karyawan->departemen->nama_departemen : 'Departemen' }}
            </p>
        </div>
    </div>

    {{-- Account Info Card --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Username / ID Card --}}
            <div class="flex items-center gap-4 p-4 bg-base-200/50 rounded-lg hover:bg-base-200 transition-colors">
                <div class="w-12 h-12 rounded-lg bg-info/10 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-identification class="h-6 w-6 text-info" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-base-content/60 mb-1">ID Card</p>
                    <p class="font-semibold text-base-content truncate">
                        {{ $karyawan ? $karyawan->id_card : '-' }}
                    </p>
                </div>
            </div>

            {{-- Jabatan / Departemen --}}
            <div class="flex items-center gap-4 p-4 bg-base-200/50 rounded-lg hover:bg-base-200 transition-colors">
                <div class="w-12 h-12 rounded-lg bg-success/10 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-briefcase class="h-6 w-6 text-success" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-base-content/60 mb-1">Jabatan / Departemen</p>
                    <p class="font-semibold text-base-content truncate">
                        {{ $karyawan ? ucfirst($karyawan->jabatan->nama_jabatan) : '-' }} -
                        {{ $karyawan ? ucfirst($karyawan->departemen->nama_departemen) : '-' }}
                    </p>
                </div>
            </div>

            {{-- Status Akun --}}
            <div class="flex items-center gap-4 p-4 bg-base-200/50 rounded-lg hover:bg-base-200 transition-colors">
                <div
                    class="w-12 h-12 rounded-lg bg-{{ $user && $user->status == 'active' ? 'success' : 'error' }}/10 flex items-center justify-center flex-shrink-0">
                    @if ($user && $user->status == 'active')
                        <x-heroicon-o-check-circle class="h-6 w-6 text-success" />
                    @else
                        <x-heroicon-o-x-circle class="h-6 w-6 text-error" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-base-content/60 mb-1">Status Akun</p>
                    <p class="font-semibold text-base-content truncate">
                        <span
                            class="badge badge-{{ $user && $user->status == 'active' ? 'success' : 'error' }} badge-sm badge-soft">
                            {{ $user ? ucfirst($user->status) : '-' }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Terakhir Login --}}
            <div class="flex items-center gap-4 p-4 bg-base-200/50 rounded-lg hover:bg-base-200 transition-colors">
                <div class="w-12 h-12 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-clock class="h-6 w-6 text-warning" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-base-content/60 mb-1">Terakhir Login</p>
                    <p class="font-semibold text-base-content truncate">
                        @if ($user && $user->last_login_at)
                            {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                        @else
                            Baru Pertama Kali
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Menu Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden">
        {{-- Header Menu --}}
        {{-- <div class="bg-primary px-6 py-4">
                <h2 class="text-lg font-bold text-primary-content">PROFILE</h2>
            </div> --}}

        {{-- Menu Items --}}
        <div class="divide-y divide-base-300">
            {{-- Informasi Akun --}}
            <button wire:click="openModal('informasi-akun')"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-user class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Informasi Akun</h3>
                        <p class="text-sm text-base-content/60">Username & status akun</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </button>

            {{-- Data Pribadi --}}
            <button wire:click="openModal('data-pribadi')"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-document-text class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Data Pribadi</h3>
                        <p class="text-sm text-base-content/60">Informasi personal karyawan</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </button>

            {{-- Foto & Identitas --}}
            <button wire:click="openModal('foto-identitas')"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-camera class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Foto & Identitas</h3>
                        <p class="text-sm text-base-content/60">Foto profil & dokumen identitas</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </button>

            {{-- Keamanan Akun --}}
            <button wire:click="openModal('keamanan-akun')"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-lock-closed class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Keamanan Akun</h3>
                        <p class="text-sm text-base-content/60">Ubah password & keamanan</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </button>

            {{-- Informasi Sistem --}}
            <button wire:click="openModal('informasi-sistem')"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-information-circle class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Informasi Sistem</h3>
                        <p class="text-sm text-base-content/60">Detail sistem & aplikasi</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </button>

            {{-- Logout --}}
            <button wire:click="logout" wire:confirm="Apakah Anda yakin ingin keluar?"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-error/10 transition-colors group">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center group-hover:bg-error/20 transition-colors">
                        <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5 text-error" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-error">Logout</h3>
                        <p class="text-sm text-error/70">Keluar dari aplikasi</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-error/40" />
            </button>
        </div>
    </div>


    {{-- Include Modals --}}
    @livewire('karyawan.profile.modals.informasi-akun')
    @livewire('karyawan.profile.modals.data-pribadi')
    @livewire('karyawan.profile.modals.foto-identitas')
    @livewire('karyawan.profile.modals.keamanan-akun')
    @livewire('karyawan.profile.modals.informasi-sistem')
</div>
