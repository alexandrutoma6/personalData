<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class FavoriteHeart extends Field
{
    protected string $view = 'forms.components.favorite-heart';
    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->afterStateHydrated(static function (FavoriteHeart $component, $state): void {
            $component->state((bool) $state);
        });

        $this->rule('boolean');
    }
}
