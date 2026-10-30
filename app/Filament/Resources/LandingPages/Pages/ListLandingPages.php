<?php

namespace App\Filament\Resources\LandingPages\Pages;

use App\Filament\Resources\LandingPages\LandingPageResource;
use App\Filament\Resources\Pages\Pages\ListPages;
use Filament\Actions\CreateAction;

class ListLandingPages extends ListPages
{
    protected static string $resource = LandingPageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('New landing page')];
    }
}
