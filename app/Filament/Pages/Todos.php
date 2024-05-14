<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Todos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-list';
    protected static string $view = 'filament.pages.todos';
}
