<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class FlashCards extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $activeNavigationIcon = 'heroicon-s-credit-card';

    protected static string $view = 'filament.pages.flash-cards';
}
