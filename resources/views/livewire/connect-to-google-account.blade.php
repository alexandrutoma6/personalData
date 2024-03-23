<?php

use Livewire\Volt\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\ActionSize;
use App\Livewire\Widgets\CalendarWidget;

new class extends Component implements HasForms, HasActions {
    use InteractsWithActions;
    use InteractsWithForms;

    public function logoutAction(): Action
    {
        return Action::make('logout')
            ->label('Logout')
            ->color('critical')
            ->outlined()
            ->size(ActionSize::Small)
            ->requiresConfirmation()
            ->action(fn() => redirect()->route('google-calendar.disconnect'));
    }
};
?>

<div class="mb-6 flex justify-between items-center">
    @if ($googleToken = \App\Models\GoogleToken::where('user_id', auth()->user()->id)->first())
        <div class="flex  items-center">
            <p class="px-1 align-text-middle font-bold">Connected to Google as:</p>
            <p class="px-1 align-middle font-medium">{{ $googleToken->email }}</p>
            <p class="px-1">{{ $this->logoutAction() }}</p>
        </div>
    @else
        <div>
            <a href="{{ App\Services\GoogleCalendar::getLoginUrl() }}"
                class="rounded-lg bg-primary border-2 hover:bg-primary font-bold py-2 px-4">Connect with Google
                Account</a>

        </div>
    @endif
    <x-filament-actions::modals />
</div>
