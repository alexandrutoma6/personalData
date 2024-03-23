<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Code extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static string $view = 'filament.pages.code';
    protected static ?string $navigationLabel = 'Code Compiler';
    protected static ?string $title = 'Code Compiler';

    
}
