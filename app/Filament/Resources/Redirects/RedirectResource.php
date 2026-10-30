<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Resources\Redirects\Pages\ManageRedirects;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnRight;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Redirects';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('from_path')->label('Old URL (from)')->required()->maxLength(255)->placeholder('/old-page')
                ->helperText('Only the path, e.g. /old-page')
                ->dehydrateStateUsing(fn ($state) => Redirect::normalize((string) $state))
                ->unique(ignoreRecord: true),
            TextInput::make('to_url')->label('New URL (to)')->required()->maxLength(500)->placeholder('/new-page  or  https://example.com/page'),
            Select::make('code')->label('Type')->options([301 => '301 – Moved permanently (best for SEO)', 302 => '302 – Temporary'])->default(301)->selectablePlaceholder(false),
            Toggle::make('is_active')->label('Active')->default(true)->inline(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('from_path')->label('From')->searchable()->weight('bold'),
            TextColumn::make('to_url')->label('To')->searchable()->limit(60),
            TextColumn::make('code')->badge()->color('gray'),
            TextColumn::make('hits')->label('Visits')->sortable(),
            IconColumn::make('is_active')->label('Active')->boolean(),
        ])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ManageRedirects::route('/')];
    }
}
