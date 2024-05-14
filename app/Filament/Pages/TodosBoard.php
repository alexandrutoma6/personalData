<?php

namespace App\Filament\Pages;

use App\Models\Todo;
use Illuminate\Support\Collection;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class TodosBoard extends KanbanBoard
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-check';
    protected static string $model = Todo::class;
    protected static string $recordTitleAttribute = 'task';

    protected function statuses(): Collection
    {
        return collect([
            ['id' => 'pending', 'title' => 'Pending'],
            ['id' => 'done', 'title' => 'Done'],
        ]);
    }
    protected function records(): Collection
    {
        return Todo::byOwner(auth()->id())->latest()->get();
    }
    public function onStatusChanged(int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        Todo::find($recordId)->update(['status' => $status]);
    }

}
