@props([
    'id',
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
    'viewMethod' => 'view',
    'editMethod' => 'edit',
    'deleteMethod' => 'confirmDelete',
    'customActions' => []
])

<div class="dropdown dropdown-end">
    <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
        <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
    </label>
    <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-300 rounded-box w-52 border border-base-100">
        @if($showView)
            <li>
                <button wire:click="{{ $viewMethod }}({{ $id }})" class="flex items-center gap-2">
                    <x-heroicon-o-eye class="w-4 h-4" />
                    <span>Lihat Detail</span>
                </button>
            </li>
        @endif

        @if($showEdit)
            <li>
                <button wire:click="{{ $editMethod }}({{ $id }})" class="flex items-center gap-2">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    <span>Edit</span>
                </button>
            </li>
        @endif

        @foreach($customActions as $action)
            <li>
                <button wire:click="{{ $action['method'] }}({{ $id }})" class="flex items-center gap-2 {{ $action['class'] ?? '' }}">
                    @if(isset($action['icon']))
                        {!! $action['icon'] !!}
                    @endif
                    <span>{{ $action['label'] }}</span>
                </button>
            </li>
        @endforeach

        @if($showDelete)
            <li>
                <button wire:click="{{ $deleteMethod }}({{ $id }})" class="flex items-center gap-2 text-error hover:bg-error hover:text-error-content">
                    <x-heroicon-o-trash class="w-4 h-4" />
                    <span>Hapus</span>
                </button>
            </li>
        @endif
    </ul>
</div>