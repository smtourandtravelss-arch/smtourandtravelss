<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Posts\PostResource;

class EditPost extends EditPage
{
    protected static string $resource = PostResource::class;
}
