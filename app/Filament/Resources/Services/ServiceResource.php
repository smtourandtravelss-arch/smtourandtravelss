<?php

namespace App\Filament\Resources\Services;

use App\Filament\Support\SiteFields as F;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Cabs & Fares (cards)';

    protected static ?string $modelLabel = 'cab';

    protected static ?string $pluralModelLabel = 'cabs';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'lg' => 3])->columnSpanFull()->schema([
                Group::make()->columnSpan(['lg' => 2])->schema([
                    Section::make('Cab card')->description('Cards show in every "Cab Cards" section and in the "Select car" list of every booking form.')->columns(2)->schema([
                        TextInput::make('title')->label('Cab name')->placeholder('Toyota Innova')->required()->maxLength(120)->columnSpanFull(),
                        TextInput::make('subtitle')->label('Models line under the name')->placeholder('Swift Dzire · Toyota Etios or similar')->maxLength(160)->columnSpanFull(),
                        TextInput::make('badge')->label('Ribbon on the picture (top left)')->placeholder('Most Popular')->maxLength(60)
                            ->helperText('Empty = no ribbon. Cards with a ribbon get a highlighted border.'),
                        TextInput::make('label')->label('Small tag next to the name')->placeholder('Budget / Family / Premium')->maxLength(40),
                        TextInput::make('category')->label('Group (optional)')->placeholder('e.g. Outstation')->maxLength(60)
                            ->helperText('Leave empty normally. Cards with a group can be shown on their own on a page.')
                            ->datalist(fn () => Service::query()->whereNotNull('category')->distinct()->pluck('category')->all()),
                        TextInput::make('button_text')->label('Book button text')->placeholder('Empty = Book Now')->maxLength(40),
                        Textarea::make('text')->label('Best for (line above the price)')->placeholder('Best for: family airport transfers & tours')->rows(2)->columnSpanFull(),
                        Repeater::make('features')->label('Specs chips')->simple(TextInput::make('text')->required()->maxLength(60))
                            ->helperText('e.g. "7+1 Seats", "4 Bags", "AC". Words like seat / bag / AC get a matching icon automatically.')
                            ->reorderable()->addActionLabel('Add chip')->defaultItems(0)->columnSpanFull(),
                    ]),
                    Section::make('Fare')->description('A number like 1800 shows as ₹1,800. "Get Quote" mode shows the text from Site Settings → Service Card Style.')
                        ->columns(4)->collapsible()->schema([
                            Select::make('price_mode')->label('What to show')->columnSpanFull()
                                ->options(['price' => 'Show fare', 'request' => 'Show "Get Quote" (text is set in Service Card Style)', 'hide' => 'Hide fare'])
                                ->default('price')->selectablePlaceholder(false)->live(),
                            TextInput::make('price_prefix')->label('Small word above fare')->placeholder('Starting')
                                ->visible(fn (Get $get) => in_array($get('price_mode'), ['price', 'request', null], true)),
                            TextInput::make('price')->label('Price')->visible(fn (Get $get) => in_array($get('price_mode'), ['price', null], true)),
                            TextInput::make('old_price')->label('Old price (crossed)')->visible(fn (Get $get) => in_array($get('price_mode'), ['price', null], true)),
                            TextInput::make('price_note')->label('After fare')->placeholder('+ fuel')
                                ->visible(fn (Get $get) => in_array($get('price_mode'), ['price', 'request', null], true)),
                        ]),
                ]),
                Group::make()->schema([
                    Section::make('Car picture')->schema([
                        F::image('image', 'services', 'Car picture (side view, transparent PNG / SVG / WebP, about 520 × 210)'),
                        F::alt('image_alt'),
                    ]),
                    Section::make('Visibility')->schema([
                        Toggle::make('is_active')->label('Show on website')->default(true),
                        TextInput::make('sort_order')->label('Position')->numeric()->default(fn () => (int) Service::max('sort_order') + 1)
                            ->helperText('1 = first. You can also drag rows in the list.'),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')->disk('site')->label('')->imageHeight(48)->extraImgAttributes(['style' => 'border-radius:8px;object-fit:cover;width:76px']),
                TextColumn::make('title')->searchable()->weight('bold')->description(fn (Service $r) => $r->badge),
                TextColumn::make('label')->label('Tag')->badge()->color('info')->placeholder('—'),
                TextColumn::make('price')->formatStateUsing(fn ($state) => is_numeric(str_replace([',', ' '], '', (string) $state)) ? '₹'.$state : $state)->placeholder('—')->toggleable(),
                ToggleColumn::make('is_active')->label('Show'),
            ])
            ->recordActions([
                EditAction::make(),
                ReplicateAction::make()->label('Copy')->mutateRecordDataUsing(function (array $data) {
                    $data['title'] .= ' (copy)';
                    $data['is_active'] = false;
                    $data['sort_order'] = (int) Service::max('sort_order') + 1;

                    return $data;
                }),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}

