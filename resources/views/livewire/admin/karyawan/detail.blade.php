<div>

    <!-- Statistik -->
    <div class="stats stats-vertical lg:stats-horizontal bg-base-100 border border-base-300 w-full mb-4">
        <div class="stat">
            <div class="stat-figure text-primary">
                <x-heroicon-o-calendar class="w-8 h-8" />
            </div>
            <div class="stat-title">Total Absensi</div>
            <div class="stat-value text-primary">{{ $karyawan->absensi_count }}</div>
            <div class="stat-desc">Total kehadiran karyawan</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-heroicon-o-document-text class="w-8 h-8" />
            </div>
            <div class="stat-title">Total Izin</div>
            <div class="stat-value text-secondary">{{ $karyawan->izin_count }}</div>
            <div class="stat-desc">Permohonan izin yang diajukan</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-accent">
                <x-heroicon-o-calendar-days class="w-8 h-8" />
            </div>
            <div class="stat-title">Total Cuti</div>
            <div class="stat-value text-accent">{{ $karyawan->cuti_count }}</div>
            <div class="stat-desc">Cuti yang telah diambil</div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                {{-- data pribadi --}}
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h4 class="card-title text-base flex items-center gap-2 mb-4">
                            <x-heroicon-o-identification class="w-5 h-5 text-primary" />
                            Data Pribadi
                        </h4>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            {{-- NIP --}}
                            <fieldset>
                                <legend class="fieldset-legend">NIP</legend>
                                <label
                                    class="input w-full tabular-nums input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-hashtag class="w-4 h-4 opacity-70" />
                                    <input type="tel" value="{{ $karyawan->nip }}" readonly
                                        class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- ID Card --}}
                            <fieldset>
                                <legend class="fieldset-legend">ID Card</legend>
                                <label
                                    class="input w-full tabular-nums input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-identification class="w-4 h-4 opacity-70" />
                                    <input type="text" value="{{ $karyawan->id_card }}" readonly
                                        class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- Nama Lengkap --}}
                            <fieldset>
                                <legend class="fieldset-legend">Nama Lengkap</legend>
                                <label
                                    class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                                    <input type="text" value="{{ $karyawan->nama_lengkap }}" readonly
                                        class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- Tanggal Lahir --}}
                            <fieldset>
                                <legend class="fieldset-legend">Tanggal Lahir</legend>
                                <label
                                    class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                    <input type="date" value="{{ $karyawan->tanggal_lahir }}" readonly
                                        class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- Status karyawan --}}
                            <fieldset>
                                <legend class="fieldset-legend">Status Karyawan</legend>
                                <label class="cursor-not-allowed label justify-start gap-3">
                                    <input type="checkbox" class="toggle toggle-success" disabled
                                        {{ $karyawan->status === 'active' ? 'checked' : '' }} />
                                    <span class="label-text flex items-center gap-2">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-success" />
                                        Status {{ $karyawan->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </label>
                            </fieldset>

                            {{-- Jenis Kelamin --}}
                            <fieldset>
                                <legend class="fieldset-legend">Jenis Kelamin</legend>
                                <div class="flex gap-6">
                                    <label class="cursor-not-allowed label justify-start gap-2">
                                        <input type="radio" name="jenis_kelamin" class="radio" disabled
                                            {{ $karyawan->jenis_kelamin === 'L' ? 'checked' : '' }} />
                                        <span class="label-text">Laki-laki</span>
                                    </label>
                                    <label class="cursor-not-allowed label justify-start gap-2">
                                        <input type="radio" name="jenis_kelamin" class="radio" disabled
                                            {{ $karyawan->jenis_kelamin === 'P' ? 'checked' : '' }} />
                                        <span class="label-text">Perempuan</span>
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                {{-- foto karyawan --}}
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h4 class="card-title text-base flex items-center gap-2 mb-4">
                            <x-heroicon-o-camera class="w-5 h-5 text-primary" />
                            Foto Karyawan
                        </h4>

                        <div class="space-y-4">
                            @if ($karyawan->foto_karyawan)
                                {{-- Preview State --}}
                                <div class="card bg-base-200 border border-base-300 overflow-hidden">
                                    <figure
                                        class="relative bg-base-300 flex items-center justify-center max-h-[400px] min-h-[300px]">
                                        {{-- Shadow Gradient Overlay --}}
                                        <div
                                            class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-transparent pointer-events-none z-[5]">
                                        </div>

                                        <img src="{{ Storage::url($karyawan->foto_karyawan) }}"
                                            alt="{{ $karyawan->nama_lengkap }}"
                                            class="max-w-full max-h-[400px] w-auto h-auto object-contain z-[1]" />

                                        {{-- Title Container --}}
                                        <div
                                            class="absolute top-0 left-0 right-0 flex items-center justify-between p-3 z-20">
                                            <div class="flex-1 min-w-0 mr-3">
                                                <p
                                                    class="text-sm font-semibold truncate text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                                                    {{ $karyawan->nama_lengkap }}
                                                </p>
                                                <p
                                                    class="text-xs text-white/90 drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                                                    Foto Karyawan
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Face Recognition Status --}}
                                        <div class="absolute bottom-3 left-3 right-3 z-20">
                                            @if ($karyawan->wajah)
                                                {{-- Success State --}}
                                                <div class="alert glass py-2 text-sm shadow-lg">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="font-semibold">Face Recognition Aktif</p>
                                                        <p class="text-xs">Wajah telah terdaftar di sistem</p>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Warning State --}}
                                                <div class="alert alert-warning py-2 text-sm shadow-lg">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                        </path>
                                                    </svg>
                                                    <div>
                                                        <p class="font-semibold">Face Recognition Tidak Aktif</p>
                                                        <p class="text-xs">Wajah belum terdaftar di sistem</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </figure>
                                </div>
                            @else
                                {{-- No Photo State --}}
                                <div class="relative">
                                    <div
                                        class="border-2 border-dashed border-base-content/20 rounded-lg p-12 text-center">
                                        <x-heroicon-o-photo class="w-12 h-12 mx-auto mb-4 text-base-content/40" />
                                        <p class="text-lg font-medium mb-2">Tidak ada foto</p>
                                        <p class="text-sm text-base-content/60">Foto belum diupload</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Informasi Karyawan --}}
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h4 class="card-title text-base flex items-center gap-2 mb-4">
                            <x-heroicon-o-briefcase class="w-5 h-5 text-primary" />
                            Informasi Karyawan
                        </h4>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            {{-- Email --}}
                            <fieldset>
                                <legend class="fieldset-legend">Email</legend>
                                <label
                                    class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-envelope class="w-4 h-4 opacity-70" />
                                    <input type="email" value="{{ $karyawan->email }}" readonly
                                        class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- No Telepon --}}
                            <fieldset>
                                <legend class="fieldset-legend">No. Telepon</legend>
                                <label
                                    class="input w-full tabular-nums input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-phone class="w-4 h-4 opacity-70" />
                                    <input type="tel" value="{{ $karyawan->no_telepon }}" readonly
                                        class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- Jabatan --}}
                            <fieldset class="lg:col-span-2">
                                <legend class="fieldset-legend">Jabatan</legend>
                                <label class="w-full">
                                    <select disabled
                                        class="select w-full input-bordered bg-base-200 cursor-not-allowed">
                                        <option selected>
                                            {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                                            @if ($karyawan->departemen)
                                                - {{ $karyawan->departemen->nama_departemen }}
                                            @endif
                                        </option>
                                    </select>
                                </label>
                            </fieldset>

                            {{-- Alamat - Full Width --}}
                            <fieldset class="lg:col-span-2">
                                <legend class="fieldset-legend">Alamat</legend>
                                <label>
                                    <textarea readonly class="textarea textarea-bordered w-full h-24 resize-none bg-base-200 cursor-not-allowed">{{ $karyawan->alamat ?? '-' }}</textarea>
                                </label>
                            </fieldset>
                        </div>
                    </div>
                </div>

                {{-- Informasi Akun untuk login --}}
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body">
                        <h4 class="card-title text-base flex items-center gap-2 mb-4">
                            <x-heroicon-o-user-circle class="w-5 h-5 text-primary" />
                            Informasi Akun Login
                        </h4>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            {{-- Username --}}
                            <fieldset>
                                <legend class="fieldset-legend">Username</legend>
                                <label
                                    class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                                    <input type="text" value="{{ $karyawan->user->username ?? '-' }}" readonly
                                        class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- Password --}}
                            <fieldset>
                                <legend class="fieldset-legend">Password</legend>
                                <div class="join w-full">
                                    <label
                                        class="input w-full input-bordered join-item flex items-center gap-2 bg-base-200">
                                        <x-heroicon-o-lock-closed class="w-4 h-4 opacity-70" />
                                        <input type="text" value="••••••" readonly
                                            class="cursor-not-allowed font-mono tracking-wider" />
                                    </label>
                                    <button type="button" disabled
                                        class="btn btn-neutral join-item cursor-not-allowed opacity-50">
                                        <x-heroicon-o-arrow-path class="w-5 h-5" />
                                    </button>
                                    <button type="button" disabled
                                        class="btn btn-ghost join-item cursor-not-allowed opacity-50">
                                        <x-heroicon-o-clipboard class="w-5 h-5" />
                                    </button>
                                </div>
                                <p class="text-xs text-base-content/60 mt-1">Password tersembunyi untuk keamanan</p>
                            </fieldset>

                            {{-- Role --}}
                            <fieldset>
                                <legend class="fieldset-legend">Role Akun</legend>
                                <label
                                    class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-user-circle class="w-4 h-4 opacity-70" />
                                    <input type="text" value="{{ ucfirst($karyawan->user->role ?? 'Karyawan') }}"
                                        readonly class="cursor-not-allowed" />
                                </label>
                            </fieldset>

                            {{-- Status Akun --}}
                            <fieldset>
                                <legend class="fieldset-legend">Status Akun</legend>
                                <label
                                    class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                    <x-heroicon-o-check-circle
                                        class="w-4 h-4 {{ $karyawan->user->status === 'active' ? 'text-success' : 'text-error' }}" />
                                    <input type="text" value="{{ ucfirst($karyawan->user->status ?? 'Active') }}"
                                        readonly class="cursor-not-allowed" />
                                </label>
                                @if ($karyawan->user->status === 'active')
                                    <p class="text-xs text-success mt-1">✓ Status akun aktif</p>
                                @else
                                    <p class="text-xs text-error mt-1">✗ Status akun tidak aktif</p>
                                @endif
                            </fieldset>

                            {{-- Tanggal Bergabung --}}
                            <fieldset class="lg:col-span-2">
                                <legend class="fieldset-legend">Tanggal Bergabung</legend>
                                <div class="alert alert-info alert-soft py-3">
                                    <x-heroicon-o-calendar class="w-5 h-5" />
                                    <div>
                                        <p class="font-semibold">
                                            {{ \Carbon\Carbon::parse($karyawan->created_at)->format('d F Y') }}</p>
                                        <p class="text-xs">Bergabung sejak
                                            {{ \Carbon\Carbon::parse($karyawan->created_at)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- Status Ganti Password --}}
                            <fieldset class="lg:col-span-2">
                                <legend class="fieldset-legend">Status Password</legend>
                                @if($karyawan->user->harus_mengganti_password)
                                    <div class="alert alert-soft alert-warning py-3">
                                        <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                                        <div>
                                            <p class="font-semibold">Akun Belum Ganti Password</p>
                                            <p class="text-xs">Pengguna masih menggunakan password default dan harus menggantinya saat login pertama kali</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-soft alert-success py-3">
                                        <x-heroicon-o-check-circle class="w-5 h-5" />
                                        <div>
                                            <p class="font-semibold">Password Sudah Diganti</p>
                                            <p class="text-xs">Pengguna telah mengubah password default</p>
                                        </div>
                                    </div>
                                @endif
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end gap-3 mt-6">
        <a wire:navigate href="{{ route('admin.karyawan.index') }}" class="btn btn-ghost btn-sm gap-2">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
            Kembali
        </a>
        <button wire:click="copyDetailToClipboard" class="btn btn-info gap-2 btn-sm">
            <x-heroicon-o-clipboard-document class="w-5 h-5" />
            Copy Data Karyawan
        </button>
        <a wire:navigate href="{{ route('admin.karyawan.edit', $karyawan->id) }}"
            class="btn btn-primary gap-2 btn-sm">
            <x-heroicon-o-pencil class="w-5 h-5" />
            Edit
        </a>
    </div>

    <!-- Toast Notifications -->
    <div class="toast toast-end z-[9999]" style="transition: all 0.3s ease;">
        @if(session('success'))
            <div class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-check class="w-5" />
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

</div>
