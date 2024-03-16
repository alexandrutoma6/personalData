<x-filament-panels::page>
    {{-- button to the todos board --}}
    <div class="flex justify-end px-2">
        <x-filament::button 
            color="secondary" 
            icon="heroicon-m-clipboard-document-check" 
            href="/admin/todos-board"
                tag="a"
        >To Todo Board</x-filament::button>
    </div>
    @livewire('todolist')
</x-filament-panels::page>
