<dialog id="modal_informasi_akun" class="modal" wire:ignore.self>
    <div class="modal-box max-w-2xl border border-base-300">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
        </form>
        
        <h3 class="text-lg font-bold mb-4 pb-4 border-b border-base-300 flex items-center gap-2">
            <x-heroicon-o-user class="h-6 w-6 text-primary" />
            Informasi Akun
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- USERNAME --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">USERNAME</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-user-circle class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $user->username }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- ROLE --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">ROLE</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-shield-check class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ ucfirst($user->role) }}" class="grow" readonly />
                </label>
            </fieldset>
            
            {{-- STATUS AKUN --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">STATUS AKUN</legend>
                <div class="flex items-center gap-2 p-3 bg-base-200/50 rounded-lg">
                    @if($user->status == 'active')
                        <x-heroicon-o-check-circle class="w-5 h-5 text-success" />
                    @else
                        <x-heroicon-o-x-circle class="w-5 h-5 text-error" />
                    @endif
                    <span class="badge badge-{{ $user->status == 'active' ? 'success' : 'error' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </fieldset>
            
            {{-- TERAKHIR LOGIN --}}
            <fieldset class="col-span-2 sm:col-span-1">
                <legend class="fieldset-legend">TERAKHIR LOGIN</legend>
                <label class="input w-full input-bordered flex items-center gap-2">
                    <x-heroicon-o-clock class="w-4 h-4 opacity-70" />
                    <input type="text" value="{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') : 'Baru Pertama Kali' }}" class="grow" readonly />
                </label>
            </fieldset>
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
</dialog>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-informasi-akun-modal', () => {
            document.getElementById('modal_informasi_akun').showModal();
        });
        
        Livewire.on('close-informasi-akun-modal', () => {
            document.getElementById('modal_informasi_akun').close();
        });
    });
</script>
