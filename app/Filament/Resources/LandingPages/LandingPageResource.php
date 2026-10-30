<?php

namespace App\Filament\Resources\LandingPages;

use App\Filament\Resources\LandingPages\Pages\CreateLandingPage;
use App\Filament\Resources\LandingPages\Pages\EditLandingPage;
use App\Filament\Resources\LandingPages\Pages\ListLandingPages;
use App\Filament\Resources\Pages\PageResource;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

/** Landing pages for ads — own header and footer, no menu, no diverting links. */
class LandingPageResource extends PageResource
{
    protected static string $type = 'landing';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Landing Pages';

    protected static ?string $modelLabel = 'landing page';

    protected static ?string $pluralModelLabel = 'landing pages';

    protected static ?string $slug = 'landing-pages';

    public static function listPage(): string
    {
        return ListLandingPages::class;
    }

    public static function createPage(): string
    {
        return CreateLandingPage::class;
    }

    public static function editPage(): string
    {
        return EditLandingPage::class;
    }
}
