<x-filament-panels::page>
    {{-- <div x-data wire:ignore.self class="flex flex-col">
        @foreach($statuses as $status)
            @include(static::$statusView)
        @endforeach

        <div wire:ignore>
            @include(static::$scriptsView)
        </div>
    </div> --}}
    <div x-data wire:ignore.self class="container">
        <div class="row">
            <div class="flex col-md-6 pl-10">
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
