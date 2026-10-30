<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\CreateAction;

class ListPosts extends ListPages
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('New blog post')];
    }
}
