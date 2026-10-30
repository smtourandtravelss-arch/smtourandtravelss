<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

/** Blog posts — same editor as Pages, plus category, excerpt and featured image. */
class PostResource extends PageResource
{
    protected static string $type = 'post';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Blog Posts';

    protected static ?string $modelLabel = 'blog post';

    protected static ?string $pluralModelLabel = 'blog posts';

    protected static ?string $slug = 'posts';

    public static function listPage(): string
    {
        return ListPosts::class;
    }

    public static function createPage(): string
    {
        return CreatePost::class;
    }

    public static function editPage(): string
    {
        return EditPost::class;
    }
}
