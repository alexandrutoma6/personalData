<?php

namespace App\Filament\Resources\NoteResource\Pages;

use App\Filament\Resources\NoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\Builder;
use Archilex\ToggleIconColumn\Columns\ToggleIconColumn;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewNote extends ViewRecord
{
    protected static string $resource = NoteResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('title')
                            ->weight(FontWeight::ExtraBold)
                            ->columnSpanFull(),
                Infolists\Components\TextEntry::make('description')
                            ->weight(FontWeight::Medium),
            ])->columns(2);
    }
    
}
