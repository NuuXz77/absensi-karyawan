@props([
    'id',
    'showView' => false,
    'showEdit' => true,
    'showDelete' => true,
    'viewMethod' => 'view',
    'editMethod' => 'edit',
    'deleteMethod' => 'confirmDelete',
    'editModalId' => 'modal_edit',
    'deleteModalId' => 'modal_delete',
    'detailModalId' => 'modal_detail',
    'customActions' => []
])

<div class="dropdown dropdown-end" x-data="{ open: false }">
    <label tabindex="0" class="btn btn-ghost btn-sm btn-square">
        <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
    </label>
    <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-52 border border-base-300">
        @if($showView)
            <li>
                <button @click="open = true; $nextTick(() => { document.getElementById('{{ $detailModalId }}').showModal(); })" wire:click="{{ $viewMethod }}({{ $id }})" class="flex items-center gap-2">
                    <x-heroicon-o-eye class="w-4 h-4" />
                    <span>Lihat Detail</span>
                </button>
            </li>
        @endif

        @if($showEdit)
            <li>
                <button @click="open = true; $nextTick(() => { document.getElementById('{{ $editModalId }}').showModal(); })" wire:click="{{ $editMethod }}({{ $id }})" class="flex items-center gap-2">
                    <x-heroicon-o-pencil class="w-4 h-4" />
                    <span>Edit</span>
                </button>
            </li>
        @endif

        @foreach($customActions as $action)
            <li>
                <button wire:click="{{ $action['method'] }}({{ $id }})" class="flex items-center gap-2 {{ $action['class'] ?? '' }}">
                    @if(isset($action['icon']))
                        @php
                            $iconName = str_replace('heroicon-', '', $action['icon']);
                            $iconComponent = 'heroicon-' . $iconName;
                        @endphp
                        <x-dynamic-component :component="$iconComponent" class="w-4 h-4" />
                    @endif
                    <span>{{ $action['label'] }}</span>
                </button>
            </li>
        @endforeach

        @if($showDelete)
            <li>
                <button @click="open = true; $nextTick(() => { document.getElementById('{{ $deleteModalId }}').showModal(); })" wire:click="{{ $deleteMethod }}({{ $id }})" class="flex items-center gap-2 text-error hover:bg-error hover:text-error-content">
                    <x-heroicon-o-trash class="w-4 h-4" />
                    <span>Hapus</span>
                </button>
            </li>
        @endif
    </ul>
</div>