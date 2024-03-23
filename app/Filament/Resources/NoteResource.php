<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Models\Note;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Resources\Resource;
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


class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $activeNavigationIcon = 'heroicon-s-pencil-square';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->minLength(3)
                    ->maxLength(20),
                MarkdownEditor::make('description')
                    ->columnSpanFull(),
                Checkbox::make('favorite')
                    ->label('Favorite'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->where('owner_user_id', auth()->id());
            })
            ->contentGrid([
                'md' => 1,
                'xl' => 2,
            ])
            ->columns([
                Grid::make()
                    ->columns(2)
                    ->schema([
                        TextColumn::make('title')
                            ->searchable()
                            ->sortable()
                            ->bulleted()
                            ->weight(FontWeight::Bold)
                            ->size(TextColumn\TextColumnSize::Large)
                            ->columnSpan(2),
                        TextColumn::make('description')
                            ->markdown()
                            ->limit(300)
                            ->columnSpan(2),
                        ToggleIconColumn::make('favorite')
                            ->onIcon('heroicon-s-heart')
                            ->offIcon('heroicon-o-heart'),
                        TextColumn::make('date')
                            ->date()
                            ->sortable()
                            ->weight(FontWeight::Medium)
                            ->extraAttributes(['class' => 'p-3 ml-10'])
                            
                    ])

            ])
            ->filters([
                Filter::make('favorite')
                    ->query(fn (Builder $query): Builder => $query->where('favorite', true))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            //if there are no notes, a button will appear on the tabel to create a note
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create note')
                    ->url('notes/create')
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
            'view' => Pages\ViewNote::route('/{record}'),
        ];
    }
}
