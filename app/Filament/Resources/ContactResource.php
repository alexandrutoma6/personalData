<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(20),
                TextInput::make('phone_number')
                    ->required()
                    ->prefix('+')
                    ->tel()->telRegex("/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/")
                    ->length(10),
                TextInput::make('email')
                    ->required()
                    ->email(),
                Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female'
                    ]),
                TextInput::make('city')
                    ->minLength(3),
                Select::make('category')
                    ->options([
                        'family' => 'Family',
                        'work' => 'Work',
                        'school' => 'School',
                        'friends' => 'Friends'
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('gender'),
                TextColumn::make('city')->searchable(),
                TextColumn::make('category')->searchable()
            ])
            ->filters([
                // Filter::make('Male')
                //     ->query(fn (Builder $query): Builder => $query->where('gender', 'male')),
                // Filter::make('Female')
                //     ->query(fn (Builder $query): Builder => $query->where('gender', 'female'))
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female'
                    ]),
                SelectFilter::make('category')
                    ->options([
                        'work' => 'Work',
                        'school' => 'School',
                        'family' => 'Family',
                        'friends' => 'Friends'
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
