<dialog id="modal_keamanan_akun" class="modal" wire:ignore.self>
    <div class="modal-box max-w-2xl border border-base-300">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
        </form>
        
        <h3 class="text-lg font-bold mb-4 pb-4 border-b border-base-300 flex items-center gap-2">
            <x-heroicon-o-lock-closed class="h-6 w-6 text-primary" />
            Keamanan Akun
        </h3>
        
        <div class="space-y-4">
            {{-- Alert jika harus mengganti password --}}
            @if($user->harus_mengganti_password == 1)
                <div class="alert alert-warning">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    <div class="flex flex-col">
                        <span class="font-semibold">Perhatian!</span>
                        <span class="text-sm">Anda disarankan untuk mengganti password Anda demi keamanan akun.</span>
                    </div>
                </div>
            @else
                <div class="alert alert-success">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                    <div class="flex flex-col">
                        <span class="font-semibold">Password Aman</span>
                        <span class="text-sm">Password Anda sudah diperbarui. Anda dapat mengubahnya kapan saja.</span>
                    </div>
                </div>
            @endif
            
            {{-- Form Ubah Password --}}
            <form wire:submit.prevent="updatePassword">
                <div class="grid grid-cols-1 gap-4">
                    {{-- PASSWORD LAMA --}}
                    <fieldset>
                        <legend class="fieldset-legend">PASSWORD LAMA</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('password_lama') input-error @enderror">
                            <x-heroicon-o-key class="w-4 h-4 opacity-70" />
                            <input required type="password" wire:model="password_lama" placeholder="Masukkan password lama" class="grow" autocomplete="current-password" />
                        </label>
                        @error('password_lama')
                            <p class="text-error text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- PASSWORD BARU --}}
                    <fieldset>
                        <legend class="fieldset-legend">PASSWORD BARU</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('password_baru') input-error @enderror">
                            <x-heroicon-o-lock-closed class="w-4 h-4 opacity-70" />
                            <input required type="password" wire:model="password_baru" placeholder="Masukkan password baru (min. 8 karakter)" class="grow" autocomplete="new-password" />
                        </label>
                        @error('password_baru')
                            <p class="text-error text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- KONFIRMASI PASSWORD BARU --}}
                    <fieldset>
                        <legend class="fieldset-legend">KONFIRMASI PASSWORD BARU</legend>
                        <label class="input w-full validator input-bordered flex items-center gap-2 @error('password_baru_confirmation') input-error @enderror">
                            <x-heroicon-o-shield-check class="w-4 h-4 opacity-70" />
                            <input required type="password" wire:model="password_baru_confirmation" placeholder="Ulangi password baru" class="grow" autocomplete="new-password" />
                        </label>
                        @error('password_baru_confirmation')
                            <p class="text-error text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </fieldset>
                    
                    {{-- Submit Button --}}
                    <div class="flex justify-end gap-3 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm gap-2" wire:loading.attr="disabled" wire:target="updatePassword">
                            <span wire:loading.remove wire:target="updatePassword" class="flex items-center gap-2">
                                <x-heroicon-o-check class="w-5 h-5" />
                                Ubah Password
                            </span>
                            <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                <span class="loading loading-spinner loading-sm"></span>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
            
            {{-- Tips Keamanan --}}
            <div class="bg-base-200 rounded-lg p-4 mt-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-shield-check class="w-6 h-6 text-success shrink-0 mt-1" />
                    <div>
                        <h4 class="font-semibold mb-2">Tips Keamanan Akun</h4>
                        <ul class="text-sm space-y-1 list-disc list-inside text-base-content/70">
                            <li>Gunakan password minimal 8 karakter dengan kombinasi huruf, angka, dan simbol</li>
                            <li>Jangan bagikan password Anda kepada siapapun</li>
                            <li>Selalu logout setelah selesai menggunakan aplikasi</li>
                            <li>Gunakan password yang kuat dan unik</li>
                            <li>Laporkan aktivitas mencurigakan kepada administrator</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Modal Footer --}}
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" wire:click="closeModal" class="btn btn-ghost btn-sm gap-2">
                <x-heroicon-o-x-mark class="w-5 h-5" />
                Tutup
            </button>
        </div>
    </div>
    <div class="modal-backdrop" wire:click="closeModal"></div>
    
    {{-- Toast Notifications --}}
    <div class="toast toast-start z-[9999]">
        @if($showSuccess)
            <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                <x-heroicon-o-check class="w-5" />
                <span>Password berhasil diubah!</span>
            </div>
        @endif
        
        @if($showError)
            <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-x-circle class="w-5"/>
                <span>{{ $errorMessage ?: 'Gagal mengubah password!' }}</span>
            </div>
        @endif
    </div>
</dialog>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-keamanan-akun-modal', () => {
            document.getElementById('modal_keamanan_akun').showModal();
        });
        
        Livewire.on('close-keamanan-akun-modal', () => {
            document.getElementById('modal_keamanan_akun').close();
        });
        
        Livewire.on('password-updated', () => {
            // Auto hide success message setelah 3 detik
            setTimeout(() => {
                @this.set('showSuccess', false);
            }, 3000);
        });
    });
</script>
