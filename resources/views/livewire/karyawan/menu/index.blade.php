<div>
    <ul class="menu bg-base-100 border border-base-300 rounded-box w-full">
        
        {{-- Kehadiran --}}
        <li>
            <details>
                <summary>
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                    Kehadiran
                </summary>
                <ul>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-clock class="w-4 h-4" />
                            Riwayat Absensi
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-chart-bar class="w-4 h-4" />
                            Rekap Kehadiran
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-document-text class="w-4 h-4" />
                            Izin & Cuti
                        </a>
                    </li>
                </ul>
            </details>
        </li>

        {{-- Informasi Kerja --}}
        <li>
            <details>
                <summary>
                    <x-heroicon-o-briefcase class="w-5 h-5" />
                    Informasi Kerja
                </summary>
                <ul>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-calendar-days class="w-4 h-4" />
                            Jadwal Kerja
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-clock class="w-4 h-4" />
                            Shift Saya
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-map-pin class="w-4 h-4" />
                            Lokasi Kerja
                        </a>
                    </li>
                </ul>
            </details>
        </li>

        {{-- Akun --}}
        <li>
            <details>
                <summary>
                    <x-heroicon-o-user-circle class="w-5 h-5" />
                    Akun
                </summary>
                <ul>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-identification class="w-4 h-4" />
                            Data Pribadi
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-lock-closed class="w-4 h-4" />
                            Ganti Password
                        </a>
                    </li>
                </ul>
            </details>
        </li>

        {{-- Laporan --}}
        <li>
            <details>
                <summary>
                    <x-heroicon-o-document-chart-bar class="w-5 h-5" />
                    Laporan
                </summary>
                <ul>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-calendar class="w-4 h-4" />
                            Rekap Bulanan
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            Download PDF
                        </a>
                    </li>
                </ul>
            </details>
        </li>

        {{-- Bantuan --}}
        <li>
            <details>
                <summary>
                    <x-heroicon-o-question-mark-circle class="w-5 h-5" />
                    Bantuan
                </summary>
                <ul>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-4 h-4" />
                            FAQ
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="/">
                            <x-heroicon-o-megaphone class="w-4 h-4" />
                            Pengumuman
                        </a>
                    </li>
                </ul>
            </details>
        </li>

        <li class="menu-title mt-4">
            <span>Sesi</span>
        </li>

        {{-- Logout --}}
        <li>
            <form id="logout-form" method="POST" action="/logout" style="display: none;">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-error hover:bg-error hover:text-error-content">
                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                Logout
            </a>
        </li>

    </ul>
</div>
