<x-filament-panels::page>
    {{-- button to todo list --}}
    <div class="flex justify-end">
        <x-filament::button 
            color="secondary" 
            icon="heroicon-m-clipboard-document-list"
            href="/admin/todos"
            tag="a"
    >To Todo List</x-filament::button> 
    </div>
    <div x-data wire:ignore.self class="container">
        <div class="">
            <div class="flex col-md-6 pl-10 bg-red-500">
                @foreach($statuses as $status)
                <div class="p-2">
                    @include(static::$statusView)
                </div>
                @endforeach
            </div>
    
            <div class="col-md-6" wire:ignore>
                @include(static::$scriptsView)
            </div>
        </div>
    </div>
    

    @unless($disableEditModal)
        <x-filament-kanban::edit-record-modal/>
    @endunless
</x-filament-panels::page>
