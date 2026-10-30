<?php

namespace App\Filament\Support;

use App\Filament\Support\SiteFields as F;
use App\Models\Category;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;

/** Extra Page Builder sections — landing pages, offers, blog. */
class ExtraBlocks
{
    public static function all(): array
    {
        $common = fn (array $fields, bool $bg = true) => array_merge(
            [F::show()],
            $fields,
            $bg ? [Grid::make(2)->schema([F::background(), F::anchor()])] : [F::anchor()],
        );

        return [
            Block::make('pricing')->label('Pricing / Packages')->icon(Heroicon::OutlinedCurrencyRupee)
                ->schema($common([
                    ...F::head(),
                    Repeater::make('items')->label('Packages')->schema([
                        TextInput::make('name')->required()->maxLength(80),
                        TextInput::make('badge')->label('Small label (optional)')->placeholder('Most popular')->maxLength(40),
                        TextInput::make('price')->label('Price')->helperText('A number shows as ₹1,999; text like "On Request" shows as it is.'),
                        TextInput::make('note')->label('Text beside price')->placeholder('per person / month'),
                        Textarea::make('features')->label('Included (one per line)')->rows(4)->columnSpanFull(),
                        TextInput::make('button_text')->default('Book Now'),
                        F::link('button_link'),
                        TextInput::make('car')->label('Pre-select this car in the popup (optional)')->datalist(fn () => F::cabNames()),
                        Toggle::make('highlight')->label('Highlight this package')->inline(false),
                    ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add package')
                        ->itemLabel(fn (array $state) => $state['name'] ?? null)->collapsible(),
                    Grid::make(3)->schema([
                        F::trip('trip', 'Book buttons open popup on trip tab'),
                        TextInput::make('enquiry_item')->label('Enquiry for (shown in leads)')->placeholder('Outstation Taxi'),
                        TextInput::make('note')->label('Small note under the packages')->placeholder('Toll, parking & state permit extra'),
                    ]),
                ])),

            Block::make('logos')->label('Trust Badges / Logo Strip')->icon(Heroicon::OutlinedCheckBadge)
                ->schema($common([
                    TextInput::make('heading')->label('Small heading (optional)')->placeholder('Trusted by / Certified by'),
                    Repeater::make('items')->label('Logos / badges')->schema([
                        F::image('image', 'logos', 'Logo or badge'),
                        TextInput::make('alt')->label('Name (alt text)'),
                    ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add logo'),
                ])),

            Block::make('countdown')->label('Offer Countdown')->icon(Heroicon::OutlinedClock)
                ->schema($common([
                    ...F::head(),
                    DateTimePicker::make('ends_at')->label('Offer ends on')->seconds(false)->required(),
                    TextInput::make('ended_text')->label('Text after the offer ends')->default('This offer has ended.'),
                    Grid::make(2)->schema([TextInput::make('button_text')->default('Grab This Offer'), F::link('button_link')]),
                ])),

            Block::make('blog_posts')->label('Latest Blog Posts')->icon(Heroicon::OutlinedNewspaper)
                ->schema($common([
                    ...F::head(),
                    Grid::make(2)->schema([
                        TextInput::make('limit')->label('How many posts')->numeric()->default(3),
                        Select::make('category_id')->label('Only this category (optional)')
                            ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id')->all())->placeholder('All categories'),
                    ]),
                ])),

            Block::make('spacer')->label('Spacer / Divider Line')->icon(Heroicon::OutlinedMinus)
                ->schema([
                    F::show(),
                    Grid::make(2)->schema([
                        TextInput::make('height')->label('Height (px)')->numeric()->default(40),
                        Toggle::make('line')->label('Show a thin line')->inline(false),
                    ]),
                ]),
        ];
    }
}
