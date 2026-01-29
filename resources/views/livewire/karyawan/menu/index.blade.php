<div class="space-y-4">
    {{-- Kehadiran Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-base-100 px-6 py-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-primary-content" />
                <h2 class="text-base font-bold text-primary-content">KEHADIRAN</h2>
            </div>
        </div>
        <div class="divide-y divide-base-300">
            <a wire:navigate href="{{ route('karyawan.kehadiran.riwayat-absensi') }}"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-clock class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Riwayat Absensi</h3>
                        <p class="text-sm text-base-content/60">Lihat histori kehadiran Anda</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <a wire:navigate href="{{ route('karyawan.kehadiran.riwayat-cuti') }}"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-calendar-days class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Riwayat Cuti</h3>
                        <p class="text-sm text-base-content/60">Lihat histori pengajuan cuti</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <a wire:navigate href="{{ route('karyawan.kehadiran.riwayat-izin') }}"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-document-text class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Riwayat Izin</h3>
                        <p class="text-sm text-base-content/60">Lihat histori pengajuan izin</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <livewire:karyawan.menu.kehadiran.izin-cuti.choose/>
        </div>
    </div>

    {{-- Informasi Kerja Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-base-100 px-6 py-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-briefcase class="w-5 h-5 text-primary-content" />
                <h2 class="text-base font-bold text-primary-content">INFORMASI KERJA</h2>
            </div>
        </div>
        <div class="divide-y divide-base-300">
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-calendar-days class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Jadwal Kerja</h3>
                        <p class="text-sm text-base-content/60">Lihat jadwal kerja Anda</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-clock class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Shift Saya</h3>
                        <p class="text-sm text-base-content/60">Informasi shift kerja Anda</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-map-pin class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Lokasi Kerja</h3>
                        <p class="text-sm text-base-content/60">Lokasi kerja yang terdaftar</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
        </div>
    </div>

    {{-- Akun Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-base-100 px-6 py-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-user-circle class="w-5 h-5 text-primary-content" />
                <h2 class="text-base font-bold text-primary-content">AKUN</h2>
            </div>
        </div>
        <div class="divide-y divide-base-300">
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-identification class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Data Pribadi</h3>
                        <p class="text-sm text-base-content/60">Kelola data pribadi Anda</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-lock-closed class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Ganti Password</h3>
                        <p class="text-sm text-base-content/60">Ubah password akun Anda</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
        </div>
    </div>

    {{-- Laporan Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-base-100 px-6 py-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-document-chart-bar class="w-5 h-5 text-primary-content" />
                <h2 class="text-base font-bold text-primary-content">LAPORAN</h2>
            </div>
        </div>
        <div class="divide-y divide-base-300">
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-calendar class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Rekap Bulanan</h3>
                        <p class="text-sm text-base-content/60">Lihat rekap kehadiran bulanan</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-arrow-down-tray class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Download PDF</h3>
                        <p class="text-sm text-base-content/60">Unduh laporan dalam format PDF</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
        </div>
    </div>

    {{-- Bantuan Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-base-100 px-6 py-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-question-mark-circle class="w-5 h-5 text-primary-content" />
                <h2 class="text-base font-bold text-primary-content">BANTUAN</h2>
            </div>
        </div>
        <div class="divide-y divide-base-300">
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">FAQ</h3>
                        <p class="text-sm text-base-content/60">Pertanyaan yang sering ditanyakan</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
            <a wire:navigate href="/"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-base-200 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <x-heroicon-o-megaphone class="h-5 w-5 text-primary" />
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-base-content">Pengumuman</h3>
                        <p class="text-sm text-base-content/60">Lihat pengumuman terbaru</p>
                    </div>
                </div>
                <x-heroicon-o-chevron-right class="h-5 w-5 text-base-content/40" />
            </a>
        </div>
    </div>

    {{-- Logout Section --}}
    <div class="bg-base-100 border border-base-300 rounded-lg shadow-lg overflow-hidden">
        <div class="divide-y divide-base-300">
            <form id="logout-form" method="POST" action="/logout" style="display: none;">
                @csrf
            </form>
            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-error/10 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center group-hover:bg-error/20 transition-colors">
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
    
    {{-- Include Modal Choose Izin/Cuti --}}
    @livewire('karyawan.menu.kehadiran.izin-cuti.choose')
</div>
