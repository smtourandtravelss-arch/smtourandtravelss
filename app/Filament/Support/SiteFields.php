<?php

namespace App\Filament\Support;

use App\Models\Service;
use App\Support\Icons;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/** Re-usable admin form fields for the website. */
class SiteFields
{
    public const HEADING_HELP = 'Words inside [square brackets] show in the highlight colour.';

    public const HERO_HEADING_HELP = 'Words inside [square brackets] show in yellow. Example: Taxi Service in Dehradun – [Book a Taxi] in 60 Seconds';

    public const LINK_HELP = 'popup = opens booking form · page-slug (e.g. contact) = opens that page · #section = scroll · full https:// link · empty = no button';

    public const TEXT_HELP = 'Empty line = new paragraph. You can use {phone}, {email}, {address}, {business}, {city}.';

    /** Image upload saved into public/uploads/<dir> with an SEO-friendly file name. */
    public static function image(string $name, string $dir = 'general', ?string $label = null): FileUpload
    {
        return FileUpload::make($name)
            ->label($label ?? Str::headline(Str::afterLast($name, '.')))
            ->disk('site')
            ->directory('uploads/'.$dir)
            ->visibility('public')
            ->image()
            ->imageEditor()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
            ->maxSize(5120)
            ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file): string => static::fileName($file))
            ->downloadable()
            ->openable();
    }

    public static function fileName(TemporaryUploadedFile $file): string
    {
        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';

        return Str::limit($base, 60, '').'-'.Str::lower(Str::random(5)).'.'.strtolower($file->getClientOriginalExtension() ?: $file->guessExtension());
    }

    public static function alt(string $name = 'image_alt'): TextInput
    {
        return TextInput::make($name)->label('Image ALT text (for Google)')->maxLength(150)
            ->helperText('Describe the photo in a few words, ideally with your keyword.');
    }

    public static function heading(string $name = 'heading', string $label = 'Heading'): TextInput
    {
        return TextInput::make($name)->label($label)->helperText(self::HEADING_HELP)->maxLength(200);
    }

    public static function link(string $name, string $label = 'Button link', string $default = 'popup'): TextInput
    {
        return TextInput::make($name)->label($label)->default($default)->helperText(self::LINK_HELP)->maxLength(300);
    }

    /** Icon picker (icons of the website design). */
    public static function icon(string $name = 'icon', string $label = 'Icon', bool $big = false): Select
    {
        return Select::make($name)->label($label)->options($big ? Icons::all() : Icons::small())
            ->searchable()->placeholder('— no icon —');
    }

    /** Cab group (= Cabs & Fares "Group") used by Book buttons and the car list of forms. */
    public static function appliance(string $name = 'appliance', string $label = 'Cab group for booking buttons (optional)'): TextInput
    {
        return TextInput::make($name)->label($label)->maxLength(60)
            ->datalist(fn () => static::categories())
            ->helperText('Same as a "Group" in Cabs & Fares. Usually leave empty (= all cabs).');
    }

    /** Trip types of the booking form (Site Settings → Booking Form → "Trip type tabs" field). */
    public static function tripTypes(): array
    {
        try {
            foreach ((array) \App\Models\Setting::get('form_fields', []) as $f) {
                if (is_array($f) && ($f['type'] ?? '') === 'trip') {
                    $opts = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', (string) ($f['options'] ?? ''))), 'strlen'));
                    if ($opts) {
                        return array_combine($opts, $opts);
                    }
                }
            }
        } catch (\Throwable) {
        }

        return ['One Way' => 'One Way', 'Round Trip' => 'Round Trip', 'Local' => 'Local', 'Airport' => 'Airport'];
    }

    /** Select: which trip tab the booking popup opens on. */
    public static function trip(string $name = 'trip', string $label = 'Popup opens on trip tab'): Select
    {
        return Select::make($name)->label($label)->options(fn () => static::tripTypes())->placeholder('— keep current tab —');
    }

    /** Cab names (Admin → Cabs & Fares) for "pre-select this car" fields. */
    public static function cabNames(): array
    {
        try {
            return Service::query()->orderBy('sort_order')->pluck('title')->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public static function categories(): array
    {
        try {
            return Service::query()->whereNotNull('category')->where('category', '!=', '')->distinct()->orderBy('category')->pluck('category')->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** small / heading / text group for a section. */
    public static function head(string $prefix = ''): array
    {
        return [
            TextInput::make($prefix.'small')->label('Small text above heading')->maxLength(120),
            static::heading($prefix.'heading'),
            Textarea::make($prefix.'text')->label('Paragraph')->rows(3)->helperText(self::TEXT_HELP)->columnSpanFull(),
        ];
    }

    public static function show(string $name = 'show', string $label = 'Show this section'): Toggle
    {
        return Toggle::make($name)->label($label)->default(true)->inline(false);
    }

    public static function points(string $name, string $label = 'Tick points', string $add = 'Add point'): Repeater
    {
        return Repeater::make($name)->label($label)
            ->simple(TextInput::make('text')->required()->maxLength(200))
            ->reorderable()->defaultItems(0)->addActionLabel($add);
    }

    public static function stats(string $name = 'items'): Repeater
    {
        return Repeater::make($name)->label('Numbers')
            ->schema([
                TextInput::make('value')->label('Big number')->required()->maxLength(20),
                TextInput::make('label')->label('Small text')->maxLength(80),
            ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add number')
            ->itemLabel(fn (array $state) => $state['value'] ?? null)->collapsible();
    }

    /** icon + heading + text list (features, steps, why-us points, boxes). */
    public static function iconItems(string $name = 'items', string $label = 'Items', bool $text = true): Repeater
    {
        return Repeater::make($name)->label($label)
            ->schema(array_values(array_filter([
                static::icon(),
                TextInput::make('title')->label('Heading')->required()->maxLength(120),
                $text ? Textarea::make('text')->label('Text')->rows(2)->columnSpanFull() : null,
            ])))->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add item')
            ->itemLabel(fn (array $state) => $state['title'] ?? null)->collapsible()->collapsed();
    }

    public static function faq(string $name = 'items'): Repeater
    {
        return Repeater::make($name)->label('Questions')
            ->schema([
                TextInput::make('question')->required()->maxLength(250),
                Textarea::make('answer')->required()->rows(3)->helperText('{phone} = your number'),
            ])->reorderable()->defaultItems(0)->addActionLabel('Add question')
            ->itemLabel(fn (array $state) => $state['question'] ?? null)->collapsible()->collapsed();
    }

    public static function testimonials(string $name = 'items'): Repeater
    {
        return Repeater::make($name)->label('Reviews (please add only real customer reviews)')
            ->schema([
                TextInput::make('name')->label('Customer name')->required()->maxLength(80),
                TextInput::make('area')->label('Area / City')->maxLength(80),
                Select::make('rating')->label('Stars')->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★'])->default(5)->selectablePlaceholder(false),
                Textarea::make('text')->label('Review')->required()->rows(3)->columnSpanFull(),
            ])->columns(3)->reorderable()->defaultItems(0)->addActionLabel('Add review')
            ->itemLabel(fn (array $state) => $state['name'] ?? null)->collapsible()->collapsed();
    }

    public static function photoCards(string $name = 'items'): Repeater
    {
        return Repeater::make($name)->label('Photo cards')
            ->schema([
                static::image('image', 'cards', 'Photo'),
                Grid::make(1)->schema([
                    TextInput::make('title')->required()->maxLength(120),
                    TextInput::make('text')->label('Small text')->maxLength(150),
                    static::link('link', 'Click opens'),
                    static::alt(),
                ])->columnSpan(1),
            ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add card')
            ->itemLabel(fn (array $state) => $state['title'] ?? null)->collapsible();
    }

    public static function background(string $default = 'white'): Select
    {
        return Select::make('background')->label('Background')->options(['white' => 'White', 'light' => 'Light blue-grey'])->default($default)->selectablePlaceholder(false);
    }

    public static function anchor(string $placeholder = ''): TextInput
    {
        return TextInput::make('anchor')->label('Section ID (optional)')->placeholder($placeholder)
            ->helperText('For menu links like #faq. Letters, numbers and - only.')->maxLength(40);
    }

    /** Page Builder sections. */
    public static function builder(string $name = 'blocks', string $label = 'Page sections'): Builder
    {
        return Builder::make($name)
            ->label($label)
            ->blocks(static::blocks())
            ->collapsible()
            ->collapsed()
            ->cloneable()
            ->reorderableWithButtons()
            ->blockNumbers(false)
            ->addActionLabel('Add a section')
            ->blockPickerColumns(3)
            ->blockPickerWidth('3xl')
            ->columnSpanFull();
    }

    /** All Page Builder sections (design sections + extra). */
    public static function blocks(): array
    {
        return array_merge(static::coreBlocks(), ExtraBlocks::all());
    }

    public static function coreBlocks(): array
    {
        $common = fn (array $fields, bool $bg = true, string $anchor = '', string $bgDefault = 'white') => array_merge(
            [static::show()],
            $fields,
            $bg ? [Grid::make(2)->schema([static::background($bgDefault), static::anchor($anchor)])] : [static::anchor($anchor)],
        );

        return [
            Block::make('hero_form')->label('Banner + Booking Form')->icon(Heroicon::OutlinedRocketLaunch)
                ->schema([
                    static::show(),
                    Grid::make(2)->schema([
                        static::image('image', 'banners', 'Background photo (1600 × 900)'),
                        Grid::make(1)->schema([
                            TextInput::make('pill')->label('Small label above heading')->maxLength(120),
                            static::appliance(),
                        ]),
                    ]),
                    TextInput::make('heading')->label('Main heading (H1)')->helperText(self::HERO_HEADING_HELP)->maxLength(200)->required(),
                    Textarea::make('text')->label('Paragraph')->rows(3)->helperText(self::TEXT_HELP),
                    Grid::make(2)->schema([
                        static::points('points', 'Tick points (2 per row)'),
                        static::points('types', 'Small chips under the buttons', 'Add chip'),
                    ]),
                    Section::make('Buttons')->compact()->columns(3)->schema([
                        Toggle::make('show_call')->label('Show Call button')->default(true)->inline(false),
                        TextInput::make('call_text')->label('Call button text')->default('Call {phone}')->helperText('{phone} = your number'),
                        Toggle::make('show_wa')->label('Show WhatsApp button')->default(true)->inline(false),
                        TextInput::make('wa_text')->label('WhatsApp button text')->default('WhatsApp Us'),
                        Textarea::make('wa_message')->label('WhatsApp message (optional)')->rows(2)->columnSpan(2)
                            ->helperText('Empty = "I need help with: <appliance> repair" (or the general message).'),
                    ]),
                    Section::make('Booking form on the banner')->compact()->columns(2)->schema([
                        Toggle::make('form_show')->label('Show the form')->default(true)->inline(false),
                        TextInput::make('form_badge')->label('Small badge above the form heading (optional)')->default(''),
                        TextInput::make('form_heading')->label('Form heading')->default('Book Your Cab'),
                        TextInput::make('form_text')->label('Text under form heading'),
                        TextInput::make('submit_text')->label('Button text (optional)')->placeholder('Empty = from Site Settings'),
                        TextInput::make('form_name')->label('Form name (shown in enquiries)')->default('Page form'),
                        static::trip('trip', 'Form opens on trip tab'),
                    ]),
                    static::anchor(),
                ]),

            Block::make('features')->label('Trust Badges Strip (under the banner)')->icon(Heroicon::OutlinedSparkles)
                ->schema([static::show(), static::iconItems('items', 'Badges (up to 6)')->maxItems(6), static::anchor()]),

            Block::make('types')->label('Service Tiles (icon + title + link)')->icon(Heroicon::OutlinedSquaresPlus)
                ->schema($common([
                    ...static::head(),
                    Toggle::make('big')->label('Big centred tiles')->inline(false),
                    Repeater::make('items')->label('Tiles')->schema([
                        static::icon('icon', 'Icon', true),
                        TextInput::make('title')->required()->maxLength(80),
                        TextInput::make('text')->label('Small text')->maxLength(160)->columnSpanFull(),
                        static::link('link', 'Click opens', 'popup'),
                        TextInput::make('link_text')->label('Link text')->placeholder('Book now'),
                        static::trip('trip', 'Popup opens on trip tab (when link = popup)'),
                        TextInput::make('car')->label('Pre-select this car (optional)')->datalist(fn () => static::cabNames()),
                    ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add tile')
                        ->itemLabel(fn (array $state) => $state['title'] ?? null)->collapsible()->collapsed(),
                ], true, 'services')),

            Block::make('trip_options')->label('One Way / Round Trip Cards')->icon(Heroicon::OutlinedArrowsRightLeft)
                ->schema($common([
                    ...static::head(),
                    Repeater::make('items')->label('Trip cards')->schema([
                        static::icon('icon', 'Icon'),
                        TextInput::make('title')->required()->maxLength(60)->placeholder('One Way Taxi'),
                        TextInput::make('subtitle')->label('Line under title')->maxLength(120)->columnSpanFull(),
                        Textarea::make('points')->label('Tick points (one per line)')->rows(4)->columnSpanFull(),
                        TextInput::make('button_text')->label('Button text')->placeholder('Book One Way'),
                        static::trip('trip', 'Button opens popup on trip tab'),
                        Toggle::make('highlight')->label('Highlight (yellow border + yellow button)')->inline(false),
                        TextInput::make('ribbon')->label('Small ribbon (optional)')->placeholder('Best for tours')->maxLength(40),
                    ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add trip card')
                        ->itemLabel(fn (array $state) => $state['title'] ?? null)->collapsible()->collapsed(),
                ], true, 'trip', 'light')),

            Block::make('routes')->label('Popular Routes')->icon(Heroicon::OutlinedMap)
                ->schema($common([
                    ...static::head(),
                    Grid::make(2)->schema([
                        TextInput::make('from')->label('From city')->placeholder('Dehradun')->helperText('Empty = city from Site Settings → SEO & Google Business.'),
                        static::trip('trip', 'Route buttons open popup on trip tab'),
                    ]),
                    Repeater::make('items')->label('Routes')->schema([
                        TextInput::make('to')->label('To city')->required()->maxLength(60),
                        TextInput::make('distance')->label('Small text (distance)')->placeholder('~100 km')->maxLength(30),
                    ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add route')
                        ->itemLabel(fn (array $state) => $state['to'] ?? null),
                    TextInput::make('note')->label('Small note under routes')->maxLength(200),
                ], true, '', 'light')),

            Block::make('trust')->label('Trust Factor (numbers + clients + company details)')->icon(Heroicon::OutlinedBuildingOffice2)
                ->schema($common([
                    ...static::head(),
                    static::stats('stats'),
                    Section::make('Left box – clients')->compact()->columns(2)->schema([
                        Toggle::make('clients_show')->label('Show box')->default(true)->inline(false),
                        TextInput::make('clients_title')->label('Title')->default('Trusted by businesses'),
                        Textarea::make('clients_text')->label('Text')->rows(2)->columnSpanFull(),
                        Repeater::make('logos')->label('Client logos')->schema([
                            static::image('image', 'logos', 'Logo'),
                            TextInput::make('alt')->label('Company name (alt text)'),
                        ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add logo')->columnSpanFull(),
                    ]),
                    Section::make('Right box – company details')->description('Name, address, phone, email and hours come from Site Settings.')->compact()->columns(3)->schema([
                        TextInput::make('company_title')->label('Title')->default('Company details'),
                        TextInput::make('company_note')->label('Line under company name')->placeholder('Registered taxi & car rental company'),
                        TextInput::make('address_label')->label('Address label')->default('Office address'),
                    ]),
                ], true, 'contact', 'light')),

            Block::make('showcase')->label('Photo Showcase (e.g. Corporate Taxi)')->icon(Heroicon::OutlinedBriefcase)
                ->schema($common([
                    Section::make('Photos')->compact()->columns(3)->schema([
                        static::image('image', 'sections', 'Big photo (1000 × 850)'),
                        static::image('image_2', 'sections', 'Small photo on the corner (600 × 400, optional)'),
                        Grid::make(1)->schema([
                            static::alt(),
                            TextInput::make('image_2_alt')->label('Small photo ALT text')->maxLength(150),
                            Select::make('image_position')->label('Photos side')->options(['left' => 'Left', 'right' => 'Right'])->default('left')->selectablePlaceholder(false),
                        ]),
                        TextInput::make('badge_big')->label('Dark badge on photo – number')->placeholder('38+')->maxLength(20),
                        TextInput::make('badge_text')->label('Dark badge – text')->placeholder('Corporate clients')->maxLength(60),
                        static::icon('badge_icon', 'Dark badge – icon'),
                    ]),
                    ...static::head(),
                    static::iconItems('items', 'Features (2 per row)')->maxItems(8),
                    Section::make('Small client logos (optional)')->compact()->collapsible()->collapsed()->schema([
                        TextInput::make('logos_title')->label('Small title above logos')->placeholder('Trusted by'),
                        Repeater::make('logos')->label('Logos')->schema([
                            static::image('image', 'logos', 'Logo'),
                            TextInput::make('alt')->label('Company name (alt text)'),
                        ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add logo')->maxItems(8),
                    ]),
                    Section::make('Buttons')->compact()->columns(3)->schema([
                        TextInput::make('button_text')->label('Yellow button text')->placeholder('Get Corporate Quote'),
                        static::link('button_link', 'Yellow button opens'),
                        static::trip('trip', 'Popup opens on trip tab'),
                        TextInput::make('enquiry_item')->label('Enquiry for (shown in leads)')->placeholder('Corporate Taxi'),
                        Toggle::make('show_call')->label('Show Call button')->default(true)->inline(false),
                        TextInput::make('call_text')->label('Call button text')->default('Call {phone}'),
                        TextInput::make('wa_text')->label('WhatsApp button text (empty = hidden)'),
                    ]),
                ], true, 'corporate')),

            Block::make('services')->label('Cab Cards (from Cabs & Fares)')->icon(Heroicon::OutlinedSquares2x2)
                ->schema($common([
                    ...static::head(),
                    Grid::make(4)->schema([
                        TextInput::make('category')->label('Only this group (optional)')->datalist(fn () => static::categories())->placeholder('All'),
                        Toggle::make('show_category')->label('Show group as tag (when no tag)')->default(false)->inline(false),
                        Toggle::make('filter')->label('Filter buttons (All / …)')->inline(false),
                        TextInput::make('limit')->label('How many cards (0 = all)')->numeric()->default(0),
                    ]),
                    Grid::make(2)->schema([TextInput::make('button_text')->label('Button under cards (optional)'), static::link('button_link')]),
                ], true, 'services')),

            Block::make('problems')->label('Problems ("Sound familiar?")')->icon(Heroicon::OutlinedExclamationTriangle)
                ->schema($common([
                    Grid::make(2)->schema([
                        static::image('image', 'sections', 'Photo (900 × 1100)'),
                        Grid::make(1)->schema([
                            TextInput::make('alert_title')->label('White box on photo – title'),
                            TextInput::make('alert_text')->label('White box on photo – text'),
                            static::alt(),
                        ]),
                    ]),
                    ...static::head(),
                    static::points('items', 'Problems', 'Add problem'),
                    Grid::make(3)->schema([TextInput::make('button_text')->label('Button text'), static::link('button_link'), static::appliance()]),
                ])),

            Block::make('steps')->label('How It Works (dark steps)')->icon(Heroicon::OutlinedListBullet)
                ->schema($common([...static::head(), static::iconItems('items', 'Steps (numbered automatically)')], false, 'how')),

            Block::make('why')->label('Why Choose Us (photos + points)')->icon(Heroicon::OutlinedShieldCheck)
                ->schema($common([
                    Grid::make(3)->schema([
                        static::image('image', 'sections', 'Big photo (1000 × 1100)'),
                        static::image('small_image', 'sections', 'Small round-corner photo (500 × 500)'),
                        Grid::make(1)->schema([
                            TextInput::make('badge_big')->label('Orange box – title'),
                            TextInput::make('badge_text')->label('Orange box – text'),
                            static::alt(),
                        ]),
                    ]),
                    ...static::head(),
                    static::iconItems('items', 'Points'),
                    Grid::make(2)->schema([TextInput::make('button_text')->label('Button text (optional)'), static::link('button_link')]),
                ], true, 'why')),

            Block::make('brands')->label('Brands Strip')->icon(Heroicon::OutlinedTag)
                ->schema([
                    static::show(),
                    TextInput::make('heading')->label('Heading'),
                    static::points('items', 'Brand names', 'Add brand'),
                    Textarea::make('note')->label('Small note under brands')->rows(2),
                    static::anchor(),
                ]),

            Block::make('testimonials')->label('Customer Reviews (slider)')->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->schema($common([
                    ...static::head(),
                    static::testimonials(),
                    Grid::make(2)->schema([
                        TextInput::make('button_text')->label('Button under reviews (optional)')->placeholder('Read all reviews on Google'),
                        TextInput::make('button_link')->label('Button link')->placeholder('https://g.page/…'),
                    ]),
                ], true, 'reviews')),

            Block::make('faq')->label('FAQ + "Still have a question?" box')->icon(Heroicon::OutlinedQuestionMarkCircle)
                ->schema($common([
                    ...static::head(),
                    static::faq(),
                    Section::make('Call button beside the questions')->compact()->columns(2)->schema([
                        Toggle::make('side_call')->label('Show call button under the heading')->default(true)->inline(false),
                        TextInput::make('side_call_text')->label('Button text')->default('{phone}'),
                    ]),
                    Section::make('Dark box under the questions')->compact()->columns(2)->schema([
                        Toggle::make('help_show')->label('Show box')->default(true)->inline(false),
                        TextInput::make('help_title')->label('Title')->default('Still have a question?'),
                        TextInput::make('help_text')->label('Text')->columnSpanFull(),
                        TextInput::make('help_call_text')->label('Call button text')->default('{phone}'),
                        TextInput::make('help_wa_text')->label('WhatsApp button text')->default('WhatsApp'),
                    ]),
                ], true, 'faq', 'light')),

            Block::make('cta')->label('Yellow Call-to-Action Band')->icon(Heroicon::OutlinedMegaphone)
                ->schema([
                    static::show(),
                    Grid::make(2)->schema([
                        TextInput::make('heading')->helperText('Empty = text from Site Settings'),
                        TextInput::make('text')->helperText('Empty = text from Site Settings'),
                        TextInput::make('call_text')->label('Call button text')->placeholder('Call Now')->helperText('Write - to hide'),
                        TextInput::make('wa_text')->label('WhatsApp button text')->placeholder('WhatsApp')->helperText('Write - to hide'),
                        TextInput::make('book_text')->label('Book button text (opens popup)')->placeholder('-')->helperText('Write - to hide'),
                        static::appliance(),
                        Toggle::make('space_top')->label('Space above the banner')->default(true)->inline(false)
                            ->helperText('Turn off when the section above has a white background.'),
                    ]),
                    static::image('image', 'banners', 'Background photo (optional — empty = from Site Settings)'),
                ]),

            Block::make('split')->label('Photo + Text Rows')->icon(Heroicon::OutlinedPhoto)
                ->schema($common([
                    ...static::head(),
                    Repeater::make('rows')->label('Rows')->schema([
                        static::image('image', 'sections', 'Photo (1000 × 800)'),
                        Grid::make(1)->schema([
                            Select::make('image_position')->label('Photo side')->options(['left' => 'Left', 'right' => 'Right'])->default('left')->selectablePlaceholder(false),
                            static::alt(),
                        ]),
                        TextInput::make('small')->label('Small text above heading'),
                        static::heading(),
                        Textarea::make('text')->label('Paragraph')->rows(4)->helperText(self::TEXT_HELP)->columnSpanFull(),
                        static::points('points')->columnSpanFull(),
                        TextInput::make('button_text')->label('Button text (optional)'),
                        static::link('button_link'),
                    ])->columns(2)->reorderable()->defaultItems(1)->addActionLabel('Add row')
                        ->itemLabel(fn (array $state) => str_replace(['[', ']'], '', (string) ($state['heading'] ?? '')) ?: null)->collapsible()->collapsed(),
                ])),

            Block::make('heading_text')->label('Heading + Text')->icon(Heroicon::OutlinedBars3BottomLeft)
                ->schema($common([
                    ...static::head(),
                    Select::make('align')->options(['center' => 'Center', 'left' => 'Left'])->default('center')->selectablePlaceholder(false),
                    Grid::make(2)->schema([TextInput::make('button_text')->label('Button text (optional)'), static::link('button_link')]),
                ])),

            Block::make('rich_text')->label('Rich Text / Article')->icon(Heroicon::OutlinedDocumentText)
                ->schema($common([
                    TextInput::make('small')->label('Small text above heading'),
                    static::heading(),
                    RichEditor::make('body')->label('Content')
                        ->toolbarButtons([['bold', 'italic', 'underline', 'strike', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['table', 'attachFiles'], ['undo', 'redo']])
                        ->fileAttachmentsDisk('site')->fileAttachmentsDirectory('uploads/content')->fileAttachmentsVisibility('public')
                        ->columnSpanFull(),
                ])),

            Block::make('stats')->label('Numbers Strip')->icon(Heroicon::OutlinedChartBar)
                ->schema([
                    static::show(),
                    Select::make('style')->options(['light' => 'White boxes', 'dark' => 'Dark blue strip'])->default('light')->selectablePlaceholder(false),
                    static::stats(),
                ]),

            Block::make('icon_boxes')->label('Icon Boxes')->icon(Heroicon::OutlinedViewColumns)
                ->schema($common([...static::head(), static::iconItems()])),

            Block::make('photo_cards')->label('Photo Cards')->icon(Heroicon::OutlinedRectangleGroup)
                ->schema($common([...static::head(), static::photoCards()])),

            Block::make('areas')->label('Areas We Serve')->icon(Heroicon::OutlinedMapPin)
                ->schema($common([...static::head(), static::points('items', 'Areas', 'Add area')])),

            Block::make('form')->label('Booking Form (dark section)')->icon(Heroicon::OutlinedEnvelope)
                ->schema([
                    static::show(),
                    ...static::head(),
                    Grid::make(2)->schema([
                        TextInput::make('form_badge')->label('Small badge above the form heading (optional)'),
                        TextInput::make('form_heading')->default('Book Your Cab'),
                        TextInput::make('form_text')->default('Get fare & confirmation on WhatsApp'),
                        TextInput::make('submit_text')->label('Button text (optional)')->placeholder('Empty = from Site Settings'),
                        TextInput::make('enquiry_item')->label('Enquiry for (optional)')->helperText('Shown in the email, e.g. "AC Repair"'),
                        static::appliance(),
                        Toggle::make('show_contact')->label('Show phone / email / address')->default(true)->inline(false),
                        TextInput::make('form_name')->label('Form name (shown in enquiries)')->default('Page form'),
                        static::trip('trip', 'Form opens on trip tab'),
                    ]),
                    static::anchor(),
                ]),

            Block::make('contact')->label('Contact Boxes + Form + Map')->icon(Heroicon::OutlinedPhone)
                ->schema($common([
                    Section::make('4 boxes on top (phone, email, address come from Site Settings)')->compact()->columns(2)->schema([
                        Toggle::make('boxes_show')->label('Show boxes')->default(true)->inline(false),
                        TextInput::make('call_note')->label('Phone box – small text'),
                        TextInput::make('wa_note')->label('WhatsApp box – small text'),
                        TextInput::make('email_note')->label('Email box – small text'),
                        TextInput::make('address_note')->label('Address box – small text'),
                    ]),
                    ...static::head(),
                    Grid::make(3)->schema([
                        TextInput::make('call_text')->label('Call button text')->default('Call Now'),
                        TextInput::make('wa_text')->label('WhatsApp button text')->default('WhatsApp'),
                        Toggle::make('hours_show')->label('Show working hours')->inline(false),
                    ]),
                    Grid::make(2)->schema([
                        Toggle::make('form_show')->label('Show form')->default(true)->inline(false),
                        Toggle::make('map_show')->label('Show map')->default(true)->inline(false),
                        TextInput::make('form_heading')->default('Book Your Cab'),
                        TextInput::make('form_text'),
                        TextInput::make('submit_text')->label('Button text (optional)')->placeholder('Empty = from Site Settings'),
                        TextInput::make('form_name')->label('Form name (shown in enquiries)')->default('Contact form'),
                    ]),
                    Textarea::make('map_embed')->label('Google Map embed link')->rows(2)->helperText('Google Maps → Share → Embed a map → paste the code or the link.'),
                ])),

            Block::make('map')->label('Google Map')->icon(Heroicon::OutlinedMap)
                ->schema($common([
                    ...static::head(),
                    Textarea::make('map_embed')->label('Google Map embed link')->rows(2)->required()->helperText('Google Maps → Share → Embed a map → paste the code or the link.'),
                ])),

            Block::make('custom_box')->label('Dark Box with Buttons')->icon(Heroicon::OutlinedSparkles)
                ->schema([
                    static::show(),
                    TextInput::make('heading')->required(),
                    Textarea::make('text')->rows(2),
                    Grid::make(3)->schema([
                        TextInput::make('button_text')->default('Book Now'),
                        static::link('button_link'),
                        TextInput::make('enquiry_item')->label('Enquiry for'),
                    ]),
                    static::anchor(),
                ]),

            Block::make('gallery')->label('Photo Gallery')->icon(Heroicon::OutlinedPhoto)
                ->schema($common([
                    ...static::head(),
                    Repeater::make('images')->label('Photos')->schema([
                        static::image('image', 'gallery', 'Photo'),
                        Grid::make(1)->schema([TextInput::make('caption'), static::alt('alt')]),
                    ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add photo'),
                ])),

            Block::make('video')->label('YouTube Video')->icon(Heroicon::OutlinedPlayCircle)
                ->schema($common([
                    ...static::head(),
                    TextInput::make('url')->label('YouTube link')->required()->url()->placeholder('https://www.youtube.com/watch?v=…'),
                ])),

            Block::make('html')->label('Custom HTML / Code')->icon(Heroicon::OutlinedCodeBracket)
                ->schema([
                    static::show(),
                    Textarea::make('code')->label('HTML code')->rows(8)->required()->helperText('Advanced: any HTML, iframe or widget code.'),
                    Toggle::make('wrap')->label('Put inside a normal section (with page width)')->default(true),
                    Grid::make(2)->schema([static::background(), static::anchor()]),
                ]),
        ];
    }
}
