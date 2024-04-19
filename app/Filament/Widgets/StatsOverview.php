<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Todo;
use App\Models\Contact;
use App\Models\Event;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Todos', $this->countTodos())
                ->color('critical')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Contacts', $this->countContacts())
                ->color('secondary')
                ->chart([10, 8, 3, 9, 2, 12, 17]),
            Stat::make('Upcoming Events', $this->countUpcomingEvents())
                ->color('neutral')
                ->chart([15, 4, 17,7, 2, 10, 3, 20]),
        ];
    }

        protected function countTodos()
    {
        return Todo::query()->where('status', 'pending')->where('owner_user_id', auth()->id())->count();
    }

    protected function countContacts()
    {
        return Contact::query()->where('owner_user_id', auth()->id())->count();
    }

    protected function countUpcomingEvents()
    {
        return Event::query()->where('starts_at', '>', now())->where('owner_user_id', auth()->id())->count();
    }

}
