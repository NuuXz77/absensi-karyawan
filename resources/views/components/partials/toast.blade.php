@props(['success' => null, 'error' => null])

<div class="toast toast-start z-[9999]">
    @if ($success)
        <div wire:key="success-{{ now()->timestamp }}" class="alert alert-success flex flex-row items-center"
            x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
            <x-heroicon-o-check class="w-5" />
            <span>{{ $success }}</span>
        </div>
    @endif

    @if ($error)
        <div wire:key="error-{{ now()->timestamp }}" class="alert alert-error flex flex-row items-center"
            x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
            <x-heroicon-o-x-circle class="w-5" />
            <span>{{ $error }}</span>
        </div>
    @endif
</div>
