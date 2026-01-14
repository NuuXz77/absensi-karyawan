<div>
    <form wire:submit.prevent="save">

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
                                        class="input w-full validator tabular-nums input-bordered flex items-center gap-2  @error('nip') input-error @enderror">
                                        <x-heroicon-o-hashtag class="w-4 h-4 opacity-70" />
                                        <input required type="tel" wire:model="nip" placeholder="NIP"
                                            pattern="[0-9]*" minlength="10" maxlength="10" />
                                    </label>
                                    <p class="validator-hint hidden">NIP wajib diisi, 10 digit angka</p>
                                    @error('nip')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </fieldset>

                                {{-- ID Card --}}
                                <fieldset>
                                    <legend class="fieldset-legend">ID Card (Auto)</legend>
                                    <label
                                        class="input w-full validator tabular-nums input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed @error('id_card') input-error @enderror">
                                        <x-heroicon-o-identification class="w-4 h-4 opacity-70" />
                                        <input type="text" wire:model="id_card" readonly
                                            placeholder="Pilih Jabatan" class="cursor-not-allowed" />
                                    </label>
                                    <p class="text-xs text-success mt-1" x-show="$wire.id_card">✓ ID Card: <strong x-text="$wire.id_card"></strong></p>
                                    @error('id_card')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </fieldset>

                                {{-- Nama Lengkap --}}
                                <fieldset>
                                    <legend class="fieldset-legend">Nama Lengkap</legend>
                                    <label
                                        class="input w-full validator input-bordered flex items-center gap-2 @error('nama_lengkap') input-error @enderror">
                                        <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                                        <input required type="text" wire:model="nama_lengkap"
                                            placeholder="Nama Lengkap" />
                                    </label>
                                    <p class="validator-hint hidden">Nama lengkap wajib diisi, minimal 3 karakter</p>
                                    @error('nama_lengkap')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </fieldset>

                                {{-- Tanggal Lahir --}}
                                <fieldset>
                                    <legend class="fieldset-legend">Tanggal Lahir</legend>
                                    <label
                                        class="input w-full validator input-bordered flex items-center gap-2 @error('tanggal_lahir') input-error @enderror">
                                        <x-heroicon-o-calendar class="w-4 h-4 opacity-70" />
                                        <input required type="date" wire:model="tanggal_lahir" />
                                    </label>
                                    <p class="validator-hint hidden">Tanggal lahir wajib diisi</p>
                                    @error('tanggal_lahir')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </fieldset>

                                {{-- Status karyawan --}}
                                <fieldset>
                                    <legend class="fieldset-legend">Status Karyawan</legend>
                                    <label class="cursor-pointer label justify-start gap-3 validator">
                                        <input type="checkbox" wire:model="is_active" class="toggle toggle-success"
                                            required />
                                        <span class="label-text flex items-center gap-2">
                                            <x-heroicon-o-check-circle class="w-5 h-5 text-success" />
                                            Status Aktif
                                        </span>
                                    </label>
                                    <p class="validator-hint hidden">Status wajib di isi</p>
                                </fieldset>


                                {{-- Jenis Kelamin --}}
                                <fieldset>
                                    <legend class="fieldset-legend">Jenis Kelamin</legend>
                                    <div class="flex gap-6 validator">
                                        <label class="cursor-pointer label justify-start gap-2">
                                            <input type="radio" name="jenis_kelamin" wire:model="jenis_kelamin"
                                                value="L" class="radio" required />
                                            <span class="label-text">Laki-laki</span>
                                        </label>
                                        <label class="cursor-pointer label justify-start gap-2">
                                            <input type="radio" name="jenis_kelamin" wire:model="jenis_kelamin"
                                                value="P" class="radio" required />
                                            <span class="label-text">Perempuan</span>
                                        </label>
                                    </div>
                                    <p class="validator-hint hidden">Jenis kelamin wajib dipilih</p>
                                    @error('jenis_kelamin')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
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
                                {{-- Upload Area --}}
                                @if (!$foto_karyawan)
                                    <div class="relative">
                                        <label for="foto-upload" class="cursor-pointer">
                                            <div id="drop-area"
                                                class="border-2 border-dashed border-base-content/20 rounded-lg p-12 hover:border-primary hover:bg-base-100 transition-all duration-200 text-center"
                                                ondragover="event.preventDefault(); event.currentTarget.classList.add('!border-primary', '!bg-base-100');"
                                                ondragleave="event.currentTarget.classList.remove('!border-primary', '!bg-base-100');"
                                                ondrop="event.preventDefault(); event.currentTarget.classList.remove('!border-primary', '!bg-base-100'); 
                                                    const input = document.getElementById('foto-upload'); 
                                                    if (event.dataTransfer.files.length) { 
                                                        input.files = event.dataTransfer.files;
                                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                                    }">
                                                <x-heroicon-o-arrow-up-tray
                                                    class="w-12 h-12 mx-auto mb-4 text-base-content/40" />
                                                <p class="text-lg font-medium mb-2">Drag and Drop your files or <span
                                                        class="text-primary underline">Browse</span></p>
                                                <p class="text-sm text-base-content/60">JPG, PNG, JPEG (Max 2MB)</p>
                                            </div>
                                            <input id="foto-upload" type="file" wire:model="foto_karyawan"
                                                accept="image/jpeg,image/jpg,image/png" class="hidden" />
                                        </label>
                                        @error('foto_karyawan')
                                            <div class="alert alert-error alert-sm mt-2">
                                                <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                                                <span class="text-xs">{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                @endif

                                {{-- Preview State with Integrated Loading --}}
                                @if ($foto_karyawan)
                                    <div class="card bg-base-200 border border-base-300 overflow-hidden" x-data="{ faceStatus: 'detecting', faceMessage: 'Mendeteksi wajah...', faceDimension: 0, progress: 0 }">
                                        <figure
                                            class="relative bg-base-300 flex items-center justify-center max-h-[400px] min-h-[300px]">
                                            {{-- Shadow Gradient Overlay --}}
                                            <div
                                                class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-transparent pointer-events-none z-[5]">
                                            </div>

                                            {{-- Canvas untuk bounding box - z-index lebih tinggi --}}
                                            <canvas id="faceCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-[15]" style="object-fit: contain;"></canvas>

                                            <img src="{{ $foto_karyawan->temporaryUrl() }}" alt="Preview" id="previewImage"
                                                class="max-w-full max-h-[400px] w-auto h-auto object-contain z-[1]" 
                                                x-init="setTimeout(() => { 
                                                    if (typeof window.processFaceRecognition === 'function') {
                                                        window.processFaceRecognition(
                                                            (status, message, dimension) => {
                                                                faceStatus = status;
                                                                faceMessage = message;
                                                                faceDimension = dimension;
                                                            },
                                                            (percent) => {
                                                                progress = percent;
                                                            }
                                                        );
                                                    }
                                                }, 500)" />

                                            {{-- Title and Close Button Container --}}
                                            <div
                                                class="absolute top-0 left-0 right-0 flex items-center justify-between p-3 z-20">
                                                <div class="flex-1 min-w-0 mr-3">
                                                    <p
                                                        class="text-sm font-semibold truncate text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                                                        {{ $foto_karyawan->getClientOriginalName() }}</p>
                                                    <p
                                                        class="text-xs text-white/90 drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                                                        {{ number_format($foto_karyawan->getSize() / 1024, 2) }} KB</p>
                                                </div>

                                                {{-- Loading Progress (appears during upload) --}}
                                                <div wire:loading wire:target="foto_karyawan">
                                                    <div
                                                        class="bg-white rounded-full p-2 shadow-2xl border-4 border-primary">
                                                        <span
                                                            class="loading loading-spinner loading-lg text-primary"></span>
                                                    </div>
                                                </div>

                                                {{-- Close Button (appears after upload complete) --}}
                                                <button type="button" wire:loading.remove wire:target="foto_karyawan"
                                                    wire:click="removeFoto"
                                                    class="btn btn-circle btn-sm btn-error btn-soft shadow-lg"
                                                    title="Ganti foto">
                                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                                </button>
                                            </div>

                                            {{-- Face Recognition Status --}}
                                            <div class="absolute bottom-3 left-3 right-3 z-20">
                                                {{-- Loading State --}}
                                                <div x-show="faceStatus === 'detecting'" class="alert alert-warning py-2 text-sm shadow-lg">
                                                    <span class="loading loading-spinner loading-sm"></span>
                                                    <div class="flex-1">
                                                        <p class="font-semibold" x-text="faceMessage"></p>
                                                        <div class="w-full bg-base-300 rounded-full h-2 mt-1">
                                                            <div class="bg-warning h-2 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                                        </div>
                                                        <p class="text-xs mt-1" x-text="'Progress: ' + progress + '%'"></p>
                                                    </div>
                                                </div>
                                                
                                                {{-- Success State --}}
                                                <div x-show="faceStatus === 'success'" class="alert alert-success py-2 text-sm shadow-lg">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="font-semibold" x-text="faceMessage"></p>
                                                        <p class="text-xs" x-text="'Embedding: ' + faceDimension + ' dimensi'"></p>
                                                    </div>
                                                </div>
                                                
                                                {{-- Error State --}}
                                                <div x-show="faceStatus === 'error'" class="alert alert-error py-2 text-sm shadow-lg">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="font-semibold" x-text="faceMessage"></p>
                                                        <p class="text-xs">Pastikan foto wajah jelas dan menghadap kamera</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </figure>
                                        {{-- <div class="card-body p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 text-center">
                                                    <span class="badge badge-warning gap-1" wire:loading wire:target="foto_karyawan">
                                                        <span class="loading loading-spinner loading-xs"></span>
                                                        Uploading
                                                    </span>
                                                </div>
                                            </div>
                                        </div> --}}
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
                                        class="input w-full validator input-bordered flex items-center gap-2 @error('email') input-error @enderror">
                                        <x-heroicon-o-envelope class="w-4 h-4 opacity-70" />
                                        <input required type="email" wire:model="email"
                                            placeholder="email@example.com" />
                                    </label>
                                    <p class="validator-hint hidden">Email wajib diisi dengan format yang benar</p>
                                    @error('email')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </fieldset>

                                {{-- No Telepon --}}
                                <fieldset>
                                    <legend class="fieldset-legend">No. Telepon</legend>
                                    <label
                                        class="input w-full validator tabular-nums input-bordered flex items-center gap-2 @error('no_telepon') input-error @enderror">
                                        <x-heroicon-o-phone class="w-4 h-4 opacity-70" />
                                        <input required type="tel" wire:model="no_telepon"
                                            placeholder="08xxxxxxxxxx" pattern="[0-9]*" minlength="10"
                                            maxlength="15" />
                                    </label>
                                    <p class="validator-hint hidden">No. telepon wajib diisi, 10-15 digit angka</p>
                                    @error('no_telepon')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </fieldset>

                                {{-- Jabatan --}}
                                <fieldset class="lg:col-span-2">
                                    <legend class="fieldset-legend">Jabatan</legend>
                                    <label class="w-full validator">
                                        <select wire:model.live="jabatan" required
                                            class="select w-full input-bordered @error('jabatan') select-error @enderror">
                                            <option value="" disabled selected>Pilih Jabatan</option>
                                            @foreach ($jabatans as $jabatanItem)
                                                <option value="{{ $jabatanItem->id }}">
                                                    {{ $jabatanItem->nama_jabatan }}
                                                    @if($jabatanItem->departemen)
                                                        - {{ $jabatanItem->departemen->nama_departemen }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <p class="validator-hint hidden">Jabatan wajib dipilih</p>
                                    @error('jabatan')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </fieldset>

                                {{-- Alamat - Full Width --}}
                                <fieldset class="lg:col-span-2">
                                    <legend class="fieldset-legend">Alamat</legend>
                                    <label class="validator">
                                        <textarea wire:model="alamat" required
                                            class="textarea textarea-bordered w-full h-24 resize-none @error('alamat') textarea-error @enderror"
                                            placeholder="Masukkan alamat lengkap..."></textarea>
                                    </label>
                                    <p class="validator-hint hidden">Alamat wajib diisi</p>
                                    @error('alamat')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
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
                                    <legend class="fieldset-legend">Username (Auto)</legend>
                                    <label
                                        class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                        <x-heroicon-o-user class="w-4 h-4 opacity-70" />
                                        <input type="text" wire:model="username" readonly placeholder="Sama dengan ID Card" class="cursor-not-allowed" />
                                    </label>
                                    <p class="text-xs text-success mt-1" x-show="$wire.username">✓ Username: <strong x-text="$wire.username"></strong></p>
                                </fieldset>

                                {{-- Password with Generate Button --}}
                                <fieldset>
                                    <legend class="fieldset-legend">Password (6 Digit)</legend>
                                    <div class="join w-full">
                                        <label class="input w-full validator input-bordered join-item flex items-center gap-2 bg-base-200">
                                            <x-heroicon-o-lock-closed class="w-4 h-4 opacity-70" />
                                            <input type="text" value="{{ $generatedPassword }}" readonly class="cursor-not-allowed font-mono tracking-wider" />
                                        </label>
                                        <button type="button" wire:click="refreshPassword" class="btn btn-neutral join-item" title="Refresh Password">
                                            <x-heroicon-o-arrow-path class="w-5 h-5" />
                                        </button>
                                        <button type="button" wire:click="copyPassword" class="btn btn-ghost join-item" title="Copy Password">
                                            <x-heroicon-o-clipboard class="w-5 h-5" />
                                        </button>
                                    </div>
                                    <p class="text-xs text-warning mt-1">⚠️ Simpan password ini untuk diberikan kepada karyawan</p>
                                </fieldset>

                                {{-- Role --}}
                                <fieldset>
                                    <legend class="fieldset-legend">Role Akun</legend>
                                    <label
                                        class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                        <x-heroicon-o-user-circle class="w-4 h-4 opacity-70" />
                                        <input type="text" value="Karyawan" readonly class="cursor-not-allowed" />
                                    </label>
                                    <p class="text-xs text-base-content/60 mt-1">Role otomatis diset sebagai Karyawan</p>
                                </fieldset>

                                {{-- Status Akun --}}
                                <fieldset>
                                    <legend class="fieldset-legend">Status Akun</legend>
                                    <label
                                        class="input w-full input-bordered flex items-center gap-2 bg-base-200 cursor-not-allowed">
                                        <x-heroicon-o-check-circle class="w-4 h-4 text-success" />
                                        <input type="text" value="Active" readonly class="cursor-not-allowed" />
                                    </label>
                                    <p class="text-xs text-success mt-1">✓ Status akun diset aktif secara default</p>
                                </fieldset>

                                {{-- Harus Ganti Password --}}
                                <fieldset class="lg:col-span-2">
                                    <legend class="fieldset-legend">Keamanan Password</legend>
                                    <div class="alert alert-warning py-3">
                                        <x-heroicon-o-shield-exclamation class="w-5 h-5" />
                                        <div>
                                            <p class="font-semibold">Karyawan harus mengganti password</p>
                                            <p class="text-xs">Untuk keamanan, karyawan wajib mengganti password saat login pertama kali</p>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="card bg-info/10 border-info/20 mt-6">
            <div class="card-body p-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-light-bulb class="w-6 h-6 text-info mt-1" />
                    <div class="text-sm space-y-2">
                        <p class="font-bold text-base">📋 Informasi Penting:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="flex items-start gap-2">
                                <x-heroicon-o-key class="w-4 h-4 mt-0.5 text-info" />
                                <span><strong>Password:</strong> Akan di-generate otomatis</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <x-heroicon-o-arrow-path class="w-4 h-4 mt-0.5 text-info" />
                                <span><strong>Ganti Password:</strong> Saat login pertama</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <x-heroicon-o-camera class="w-4 h-4 mt-0.5 text-info" />
                                <span><strong>Face Recognition:</strong> Untuk absensi</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <x-heroicon-o-user-circle class="w-4 h-4 mt-0.5 text-info" />
                                <span><strong>Akun Login:</strong> Otomatis dibuat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 mt-6">
            <a wire:navigate href="{{ route('admin.karyawan.index') }}" class="btn btn-ghost btn-sm gap-2">
                <x-heroicon-o-x-mark class="w-5 h-5" />
                Batal
            </a>
            <button type="submit" class="btn btn-primary gap-2 btn-sm" wire:loading.attr="disabled"
                wire:target="save">
                <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                    <x-heroicon-o-check class="w-5 h-5" />
                    Simpan
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <span class="loading loading-spinner loading-sm"></span>
                    Menyimpan data...
                </span>
            </button>
        </div>
    </form>
</div>

{{-- Face Recognition Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global variables
        window.faceAPIModelsLoaded = false;
        window.faceAPILoading = false;
        
        // Load face-api models with timeout and progress
        async function loadFaceAPIModels(progressCallback) {
            if (window.faceAPIModelsLoaded) return true;
            if (window.faceAPILoading) {
                // Wait for loading to complete
                await new Promise(resolve => {
                    const checkInterval = setInterval(() => {
                        if (window.faceAPIModelsLoaded || !window.faceAPILoading) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 100);
                });
                return window.faceAPIModelsLoaded;
            }

            window.faceAPILoading = true;
            
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
            
            try {
                console.log('🔄 Loading Face-API models...');
                if (progressCallback) progressCallback(10);
                
                // Check if face-api is available
                if (typeof faceapi === 'undefined') {
                    throw new Error('face-api library not loaded');
                }
                
                if (progressCallback) progressCallback(20);
                
                // Load models with timeout
                const loadPromise = Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);
                
                if (progressCallback) progressCallback(40);
                
                // Set 30 second timeout for model loading
                const timeoutPromise = new Promise((_, reject) => 
                    setTimeout(() => reject(new Error('Model loading timeout')), 30000)
                );
                
                await Promise.race([loadPromise, timeoutPromise]);
                
                console.log('✓ Face-API models loaded successfully');
                window.faceAPIModelsLoaded = true;
                window.faceAPILoading = false;
                
                if (progressCallback) progressCallback(60);
                
                return true;
            } catch (error) {
                console.error('✗ Error loading Face-API models:', error);
                window.faceAPILoading = false;
                return false;
            }
        }

        // Draw bounding box on detected face (persistent)
        function drawFaceBox(canvas, detection, imageElement) {
            const ctx = canvas.getContext('2d');
            
            // Get image dimensions and position
            const imgRect = imageElement.getBoundingClientRect();
            const canvasRect = canvas.getBoundingClientRect();
            
            // Set canvas size to match container
            canvas.width = canvasRect.width;
            canvas.height = canvasRect.height;
            
            // Calculate scale factors
            const scaleX = imgRect.width / imageElement.naturalWidth;
            const scaleY = imgRect.height / imageElement.naturalHeight;
            
            // Calculate offset to center image
            const offsetX = (canvasRect.width - imgRect.width) / 2;
            const offsetY = (canvasRect.height - imgRect.height) / 2;
            
            // Don't clear canvas - we want it to persist
            // ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw bounding box
            const box = detection.detection.box;
            const x = (box.x * scaleX) + offsetX;
            const y = (box.y * scaleY) + offsetY;
            const width = box.width * scaleX;
            const height = box.height * scaleY;
            
            // Draw semi-transparent background for box
            ctx.fillStyle = 'rgba(0, 255, 0, 0.15)';
            ctx.fillRect(x, y, width, height);
            
            // Draw main box with glow effect
            ctx.shadowColor = '#00ff00';
            ctx.shadowBlur = 15;
            ctx.strokeStyle = '#00ff00';
            ctx.lineWidth = 4;
            ctx.strokeRect(x, y, width, height);
            
            // Reset shadow for other drawings
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            
            // Draw corners with thicker lines
            const cornerLength = 25;
            ctx.strokeStyle = '#00ff00';
            ctx.lineWidth = 5;
            ctx.lineCap = 'round';
            
            // Top-left
            ctx.beginPath();
            ctx.moveTo(x, y + cornerLength);
            ctx.lineTo(x, y);
            ctx.lineTo(x + cornerLength, y);
            ctx.stroke();
            
            // Top-right
            ctx.beginPath();
            ctx.moveTo(x + width - cornerLength, y);
            ctx.lineTo(x + width, y);
            ctx.lineTo(x + width, y + cornerLength);
            ctx.stroke();
            
            // Bottom-left
            ctx.beginPath();
            ctx.moveTo(x, y + height - cornerLength);
            ctx.lineTo(x, y + height);
            ctx.lineTo(x + cornerLength, y + height);
            ctx.stroke();
            
            // Bottom-right
            ctx.beginPath();
            ctx.moveTo(x + width - cornerLength, y + height);
            ctx.lineTo(x + width, y + height);
            ctx.lineTo(x + width, y + height - cornerLength);
            ctx.stroke();
            
            // Draw label with background
            const labelText = '✓ Wajah Terdeteksi';
            ctx.font = 'bold 18px Arial';
            const textMetrics = ctx.measureText(labelText);
            const textWidth = textMetrics.width;
            const textHeight = 24;
            const padding = 10;
            
            // Label background with rounded corners
            const labelX = x - 2;
            const labelY = y - textHeight - padding * 2;
            const labelWidth = textWidth + padding * 2;
            const labelHeight = textHeight + padding;
            
            ctx.fillStyle = '#00ff00';
            ctx.beginPath();
            ctx.roundRect(labelX, labelY, labelWidth, labelHeight, 6);
            ctx.fill();
            
            // Label text
            ctx.fillStyle = '#000000';
            ctx.font = 'bold 18px Arial';
            ctx.fillText(labelText, x + padding - 2, y - padding - 2);
            
            // Store box data for persistence
            canvas.dataset.hasBox = 'true';
            canvas.dataset.detectionData = JSON.stringify({
                box: detection.detection.box,
                naturalWidth: imageElement.naturalWidth,
                naturalHeight: imageElement.naturalHeight
            });
            
            console.log('✓ Bounding box drawn and persisted on canvas');
        }
        
        // Continuously redraw face box to ensure it stays visible
        function keepBoxVisible() {
            const canvas = document.getElementById('faceCanvas');
            const img = document.getElementById('previewImage');
            
            if (canvas && img && canvas.dataset.hasBox === 'true') {
                try {
                    const data = JSON.parse(canvas.dataset.detectionData);
                    const detection = { detection: { box: data.box } };
                    
                    // Redraw every frame to keep it visible
                    requestAnimationFrame(() => {
                        drawFaceBox(canvas, detection, img);
                        // Keep checking and redrawing
                        setTimeout(keepBoxVisible, 100);
                    });
                } catch (e) {
                    console.log('Could not maintain box visibility');
                }
            } else if (canvas && canvas.dataset.hasBox === 'true') {
                // Keep trying if image not ready yet
                setTimeout(keepBoxVisible, 100);
            }
        }
        
        // Redraw face box on window resize (to keep box visible)
        function setupCanvasPersistence() {
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    const canvas = document.getElementById('faceCanvas');
                    const img = document.getElementById('previewImage');
                    
                    if (canvas && img && canvas.dataset.hasBox === 'true') {
                        try {
                            const data = JSON.parse(canvas.dataset.detectionData);
                            const detection = { detection: { box: data.box } };
                            drawFaceBox(canvas, detection, img);
                        } catch (e) {
                            console.log('Could not redraw box on resize');
                        }
                    }
                }, 250);
            });
        }

        // Detect face and get embedding
        async function detectFaceEmbedding(imageElement, canvas) {
            try {
                const detection = await faceapi
                    .detectSingleFace(imageElement, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416,
                        scoreThreshold: 0.5
                    }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detection && detection.descriptor) {
                    // Draw bounding box on canvas
                    if (canvas) {
                        drawFaceBox(canvas, detection, imageElement);
                    }
                    return Array.from(detection.descriptor);
                }
                return null;
            } catch (error) {
                console.error('Error detecting face:', error);
                return null;
            }
        }

        // Process face recognition when image is loaded
        window.processFaceRecognition = async function(statusCallback, progressCallback) {
            const previewImage = document.getElementById('previewImage');
            const canvas = document.getElementById('faceCanvas');
            
            if (!previewImage) {
                console.log('Preview image not found');
                return;
            }

            // Set detecting status
            if (statusCallback) statusCallback('detecting', 'Memuat model AI...', 0);
            if (progressCallback) progressCallback(0);

            // Set 60 second timeout
            const timeoutId = setTimeout(() => {
                if (statusCallback) {
                    statusCallback('error', 'Timeout: Wajah tidak terdeteksi dalam 60 detik', 0);
                }
                console.log('✗ Face detection timeout after 60 seconds');
            }, 60000);

            try {
                // Ensure models are loaded
                if (!window.faceAPIModelsLoaded) {
                    console.log('Loading Face-API models...');
                    if (statusCallback) statusCallback('detecting', 'Mengunduh model AI...', 0);
                    
                    const modelsLoaded = await loadFaceAPIModels(progressCallback);
                    
                    if (!modelsLoaded) {
                        clearTimeout(timeoutId);
                        console.log('Failed to load models');
                        if (statusCallback) statusCallback('error', 'Gagal memuat model Face Recognition', 0);
                        return;
                    }
                }

                if (progressCallback) progressCallback(70);
                if (statusCallback) statusCallback('detecting', 'Menunggu gambar siap...', 0);

                // Wait for image to load with timeout
                if (!previewImage.complete) {
                    await Promise.race([
                        new Promise((resolve) => {
                            previewImage.onload = resolve;
                        }),
                        new Promise((resolve) => setTimeout(resolve, 10000))
                    ]);
                }

                // Verify image is loaded
                if (!previewImage.complete || previewImage.naturalWidth === 0) {
                    clearTimeout(timeoutId);
                    if (statusCallback) statusCallback('error', 'Gambar gagal dimuat', 0);
                    return;
                }

                if (progressCallback) progressCallback(80);
                if (statusCallback) statusCallback('detecting', 'Menganalisis wajah...', 0);

                // Add delay to ensure image is ready
                await new Promise(resolve => setTimeout(resolve, 500));

                console.log('Starting face detection...');
                if (progressCallback) progressCallback(90);

                // Detect face with canvas
                const embedding = await detectFaceEmbedding(previewImage, canvas);

                clearTimeout(timeoutId);

                if (embedding && embedding.length > 0) {
                    // Send embedding to Livewire
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('face-detected', { embedding: embedding });
                    }
                    
                    if (progressCallback) progressCallback(100);
                    
                    // Update status to success
                    if (statusCallback) statusCallback('success', 'Wajah berhasil terdeteksi!', embedding.length);
                    
                    console.log('✓ Face detected, embedding length:', embedding.length);
                    
                    // Start keeping the box visible continuously
                    setTimeout(() => {
                        keepBoxVisible();
                    }, 500);
                } else {
                    if (progressCallback) progressCallback(100);
                    
                    // Update status to error
                    if (statusCallback) statusCallback('error', 'Wajah tidak terdeteksi pada foto', 0);
                    
                    console.log('✗ No face detected');
                }
            } catch (error) {
                clearTimeout(timeoutId);
                console.error('✗ Face recognition error:', error);
                if (statusCallback) statusCallback('error', 'Terjadi kesalahan: ' + error.message, 0);
            }
        };

        // Initialize - preload models in background
        console.log('🚀 Initializing Face Recognition...');
        
        // Setup canvas persistence on resize
        setupCanvasPersistence();
        
        // Check if face-api is loaded
        if (typeof faceapi !== 'undefined') {
            loadFaceAPIModels((progress) => {
                console.log(`Model loading progress: ${progress}%`);
            }).then(success => {
                if (success) {
                    console.log('✓ Face-API ready');
                } else {
                    console.log('✗ Face-API initialization failed');
                }
            });
        } else {
            console.error('✗ face-api library not found. Please check CDN link.');
        }
    });

    // Handle copy password with Livewire
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('password-copied', function(event) {
            const password = event.password || window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).get('generatedPassword');
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(password).then(function() {
                    alert('✓ Password berhasil disalin: ' + password);
                }).catch(function() {
                    prompt('Copy password ini:', password);
                });
            } else {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = password;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    alert('✓ Password berhasil disalin: ' + password);
                } catch (err) {
                    prompt('Copy password ini:', password);
                }
                document.body.removeChild(textarea);
            }
        });
    });
</script>
