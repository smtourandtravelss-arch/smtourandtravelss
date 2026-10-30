<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\SiteFields as F;
use App\Models\Page;
use App\Services\SeoAnalyzer;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema, string $type = 'page'): Schema
    {
        return $schema->components([
            Tabs::make('page')->persistTabInQueryString()->columnSpanFull()->tabs([
                Tab::make('Content')->icon(Heroicon::OutlinedPencilSquare)->schema(static::contentTab($type)),
                Tab::make('SEO')->icon(Heroicon::OutlinedMagnifyingGlass)->schema(static::seoTab($type))
                    ->badge(fn (?Page $record) => $record?->seo_score !== null ? $record->seo_score.'/100' : null)
                    ->badgeColor(fn (?Page $record) => SeoAnalyzer::color((int) $record?->seo_score)),
                Tab::make('Advanced')->icon(Heroicon::OutlinedCog6Tooth)->schema([
                    Textarea::make('custom_schema')->label('Custom schema markup (JSON-LD)')->rows(10)
                        ->placeholder('{ "@context": "https://schema.org", "@type": "Product", "name": "…" }')
                        ->helperText('Paste schema markup from any generator — with or without the <script type="application/ld+json"> tags. It is added to this page only. FAQ schema is made automatically from the FAQ section; Breadcrumb schema is automatic too.')
                        ->rules([fn () => function (string $attribute, $value, \Closure $fail) {
                            $json = trim(preg_replace('~^\s*<script[^>]*>|</script>\s*$~i', '', (string) $value));
                            if ($json !== '' && json_decode($json) === null) {
                                $fail('This is not valid JSON. Please check commas and quotes.');
                            }
                        }]),
                    Textarea::make('content.head_code')->label('Extra code in <head> for this page only')->rows(6)
                        ->helperText('Example: a page-specific Google Ads tag or schema JSON-LD. Leave empty if not needed.'),
                ]),
            ]),
        ]);
    }

    /* ------------------------------------------------------------------ */
    protected static function contentTab(string $type = 'page'): array
    {
        $is = fn (string ...$templates) => fn (Get $get) => in_array($get('template'), $templates, true);

        return [
            Section::make('Page')->columns(2)->schema([
                TextInput::make('title')->label('Page name')->required()->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Get $get, Set $set, string $operation) {
                        if ($operation === 'create' && blank($get('slug_touched'))) {
                            $set('slug', PageResource::uniqueSlug(Str::slug((string) $state)));
                        }
                    }),
                TextInput::make('slug')->label('Page URL')->required()->maxLength(120)
                    ->prefix(fn () => rtrim(url('/'), '/').'/')
                    ->regex('/^[A-Za-z0-9](?:[A-Za-z0-9&-]*[A-Za-z0-9])?(?:\.php)?$/')
                    ->validationMessages(['regex' => 'Use letters, numbers and dashes only (".php" at the end is allowed), e.g. dehradun-to-mussoorie-taxi.php'])
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (?Page $record) => $record?->slug === 'home' || $record?->slug === 'thank-you')
                    ->dehydrated(fn (?Page $record) => ! in_array($record?->slug, ['home', 'thank-you'], true))
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set) => $set('slug_touched', '1'))
                    ->helperText('Short, with your keyword. Example: dehradun-to-mussoorie-taxi.php'),
                $type === 'page'
                    ? Select::make('template')->label('Design')->options(Page::TEMPLATES)->default('builder')->required()
                        ->live()->selectablePlaceholder(false)
                        ->disabled(fn (?Page $record) => (bool) $record?->is_system)
                        ->dehydrated(fn (?Page $record) => ! $record?->is_system)
                        ->helperText('"Page Builder" lets you add any sections (banner + form, service cards, FAQ …).')
                    : Hidden::make('template')->default($type)->dehydrated(),
                Toggle::make('is_published')->label('Published (visible on website)')->default(true)->inline(false)
                    ->disabled(fn (?Page $record) => $record?->slug === 'home'),
                F::appliance('content.appliance', 'Cab group of this page (optional)')
                    ->helperText('Usually empty (= all cabs). If set, the "Select car" list on this page shows only cabs of this group from Cabs & Fares.')
                    ->visible(fn (Get $get) => ! in_array($get('template'), ['thank-you', 'legal', 'blog'], true))
                    ->columnSpanFull(),
            ]),

            // Blog post details
            Section::make('Post details')->visible($type === 'post')->columns(2)->schema([
                Select::make('category_id')->label('Category')->options(fn () => \App\Models\Category::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()->placeholder('— No category —')
                    ->createOptionForm([
                        TextInput::make('name')->required()->maxLength(100),
                        TextInput::make('slug')->required()->maxLength(120)->unique('categories', 'slug')->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/'),
                    ])
                    ->createOptionUsing(fn (array $data) => \App\Models\Category::create($data)->getKey()),
                DateTimePicker::make('published_at')->label('Publish date')->default(now())->seconds(false),
                Textarea::make('excerpt')->label('Short summary (shown in blog list & Google)')->rows(2)->maxLength(300)->columnSpanFull(),
                F::image('featured_image', 'blog', 'Featured photo (1200 × 630)')->columnSpanFull(),
            ]),

            // Landing page options (this page only; defaults are in Site Settings → Landing Pages)
            Section::make('Landing page header & footer (this page)')
                ->description('Header buttons, footer text and colours for ALL landing pages are in Site Settings → Landing Pages. Here you can change only this page.')
                ->visible($type === 'landing')->collapsible()->columns(3)->schema([
                    TextInput::make('content.lp_phone')->label('Call number for this page (optional)')->placeholder('Empty = business number'),
                    TextInput::make('content.lp_button_text')->label('Header button text (optional)')->placeholder('Empty = from Site Settings'),
                    TextInput::make('content.lp_enquiry_item')->label('"Booking for" shown in email (optional)'),
                    Toggle::make('content.lp_show_call')->label('Show Call button in header')->default(true)->inline(false),
                    Toggle::make('content.lp_show_enquiry')->label('Show Book button in header')->default(true)->inline(false),
                    Toggle::make('content.lp_show_footer')->label('Show footer')->default(true)->inline(false),
                ]),

            // Inner-page banner (all designs except Home and Landing)
            Section::make('Top banner')->description('Dark banner with the page heading (H1) and breadcrumb. Pages that start with a "Banner + Booking Form" section can turn it off.')->collapsible()
                ->visible(fn (Get $get) => ! in_array($get('template'), ['home', 'landing'], true))
                ->columns(2)->schema([
                    Toggle::make('content.hero_show')->label('Show top banner')->default(true)->inline(false)
                        ->visible(fn (Get $get) => in_array($get('template'), ['builder', 'post'], true)),
                    TextInput::make('content.page_name')->label('Name in breadcrumb (Home › …)')->maxLength(100),
                    F::heading('content.hero_title', 'Main heading (H1)')->live(onBlur: true)
                        ->visible(fn (Get $get) => $get('template') !== 'thank-you'),
                    Textarea::make('content.hero_text')->label('Line under heading')->rows(2)
                        ->visible(fn (Get $get) => $get('template') !== 'thank-you'),
                    F::image('content.hero_image', 'banners', 'Banner photo (1600 × 600) — empty = default from Site Settings'),
                ]),

            // ---------------- ALL SERVICES ----------------
            Group::make()->statePath(static::templateKey('listing'))->visible($is('listing'))->schema([
                Section::make('Heading above cards')->description('The cards come from the Services menu.')->columns(2)->schema([
                    ...F::head('section1_'),
                    Toggle::make('filter_show')->label('Show filter buttons (All / appliances)')->default(true)->inline(false),
                    TextInput::make('category_filter')->label('Show only this appliance (optional)')->datalist(fn () => F::categories()),
                ]),
            ]),

            // ---------------- LONG TEXT ----------------
            Group::make()->statePath(static::templateKey('legal'))->visible($is('legal'))->schema([
                Section::make('Page text')->schema([
                    TextInput::make('last_updated')->label('"Last updated" date (shown under the heading when "Line under heading" is empty)')->placeholder('5 October 2026'),
                    RichEditor::make('body')->label('Text')
                        ->toolbarButtons([['bold', 'italic', 'underline', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['table', 'attachFiles'], ['undo', 'redo']])
                        ->fileAttachmentsDisk('site')->fileAttachmentsDirectory('uploads/content')->fileAttachmentsVisibility('public')
                        ->helperText('You can use {phone}, {email}, {address}, {business}.'),
                ]),
            ]),

            // ---------------- THANK YOU ----------------
            Group::make()->statePath(static::templateKey('thank-you'))->visible($is('thank-you'))->schema([
                Section::make('Thank-you message')->description('{name} = customer first name · {item} = service booked. Buttons are in Site Settings → Popup, Banner & Thank You.')->columns(2)->schema([
                    TextInput::make('heading_name')->label('Heading when name is known')->default('Thank You, {name}!'),
                    TextInput::make('heading_general')->label('Heading when name is not known')->default('Thank You!'),
                    Textarea::make('message_item')->label('Message when service is known')->rows(2),
                    Textarea::make('message_general')->label('Message when service is not known')->rows(2),
                    TextInput::make('small_line')->label('Bold line above the buttons')->columnSpanFull(),
                ]),
            ]),

            // ---------------- PAGE BUILDER ----------------
            Section::make('Page sections')->description('Add, move, copy or remove sections. Every section uses the website design. Hover a section name in "Add a section" to see a preview.')
                ->visible($is('builder', 'landing', 'post', 'home'))->schema([F::builder('content.blocks')]),

            Section::make('Extra sections (optional)')->description('Add more sections at the end of this page (before the footer).')
                ->visible(fn (Get $get) => ! in_array($get('template'), ['builder', 'landing', 'post', 'home'], true))->collapsible()->collapsed()
                ->schema([F::builder('content.extra_blocks', 'Extra sections')]),

            Section::make('Blue call-to-action banner at the end')->description('Tip: you can also add it as a section ("Blue Call-to-Action Banner") to choose its position.')->collapsible()->collapsed()
                ->visible($is('builder', 'post', 'listing', 'legal', 'blog'))
                ->columns(2)->schema([
                    Toggle::make('content.cta_show')->label('Show banner')->default(true)->inline(false),
                    Toggle::make('content.cta_space_top')->label('Space above the banner')->default(false)->inline(false),
                    TextInput::make('content.cta_heading')->label('Heading')->placeholder('Empty = from Site Settings'),
                    TextInput::make('content.cta_text')->label('Text')->placeholder('Empty = from Site Settings'),
                ]),
        ];
    }

    /* ------------------------------------------------------------------ */
    protected static function seoTab(string $type = 'page'): array
    {
        return [
            Grid::make(['default' => 1, 'lg' => 5])->schema([
                Group::make()->columnSpan(['lg' => 3])->schema([
                    Section::make('Search engine (Google)')->schema([
                        TextInput::make('focus_keyword')->label('Focus keyword')->maxLength(120)->live(onBlur: true)
                            ->helperText('The main phrase people search on Google, e.g. "taxi service in Dehradun". Add more with commas — the first one is checked.'),
                        TextInput::make('seo_title')->label('SEO title')->maxLength(120)->live(onBlur: true)
                            ->hint(fn (?string $state) => mb_strlen((string) $state).' / 60')
                            ->helperText('Shown as the blue link in Google. 30–60 characters. Empty = page heading + business name.'),
                        Textarea::make('seo_description')->label('Meta description')->rows(3)->maxLength(320)->live(onBlur: true)
                            ->hint(fn (?string $state) => mb_strlen((string) $state).' / 160')
                            ->helperText('The 2 lines under the title in Google. 120–160 characters. Use the keyword and a call to action.'),
                    ]),
                    Section::make('Indexing')->columns(3)->schema([
                        Toggle::make('robots_index')->label('Show in Google (index)')->default($type !== 'landing')->inline(false)->live(),
                        Toggle::make('robots_follow')->label('Follow links (follow)')->default(true)->inline(false),
                        Toggle::make('in_sitemap')->label('Include in sitemap.xml')->default($type !== 'landing')->inline(false),
                        TextInput::make('canonical_url')->label('Canonical URL (optional)')->url()->maxLength(255)->columnSpanFull()
                            ->helperText('Only if this page is a copy of another page. Empty = this page itself.'),
                        Select::make('schema_type')->label('Schema type (structured data)')->columnSpanFull()
                            ->options([
                                'WebPage' => 'Web page (normal)',
                                'AboutPage' => 'About page',
                                'ContactPage' => 'Contact page',
                                'Service' => 'Service page',
                                'Article' => 'Article / Blog',
                                'HomeAndConstructionBusiness' => 'Home service business (local business)',
                                'LocalBusiness' => 'Local business',
                                'none' => 'None',
                            ])->placeholder('Automatic'),
                    ]),
                    Section::make('Social sharing (WhatsApp / Facebook)')->collapsible()->collapsed()->columns(2)->schema([
                        TextInput::make('og_title')->label('Share title')->maxLength(150)->placeholder('Empty = SEO title'),
                        Textarea::make('og_description')->label('Share description')->rows(2)->maxLength(300)->placeholder('Empty = meta description'),
                        F::image('og_image', 'social', 'Share image (1200 × 630)')->columnSpanFull(),
                    ]),
                ]),
                Group::make()->columnSpan(['lg' => 2])->schema([
                    Section::make('SEO score')
                        ->headerActions([
                            Action::make('recheck')->label('Re-check')->icon(Heroicon::OutlinedArrowPath)->link()->action(fn () => null),
                        ])
                        ->schema([
                            View::make('filament.seo-panel')->viewData(fn ($livewire, ?Page $record) => [
                                'result' => static::liveAnalysis($livewire, $record),
                            ]),
                        ]),
                ]),
            ]),
        ];
    }

    /**
     * Each page design keeps its fields in its own form key (t_home, t_about…),
     * because different designs reuse names like "section1_items" for
     * different kinds of lists. They are merged back into "content" on save.
     */
    public const TEMPLATE_KEYS = ['listing', 'legal', 'thank-you'];

    public static function templateKey(string $template): string
    {
        return 't_'.str_replace('-', '', $template);
    }

    /** Record data → form data. */
    public static function splitContent(array $data): array
    {
        $content = static::withDefaults(is_array($data['content'] ?? null) ? $data['content'] : []);
        $data['content'] = $content;
        foreach (static::TEMPLATE_KEYS as $t) {
            $data[static::templateKey($t)] = $content;
        }

        return $data;
    }

    /** Form data → record data. */
    public static function mergeContent(array $data, ?string $template): array
    {
        $content = is_array($data['content'] ?? null) ? $data['content'] : [];
        $key = static::templateKey((string) $template);
        if (in_array($template, static::TEMPLATE_KEYS, true) && is_array($data[$key] ?? null)) {
            $content = array_replace($content, $data[$key]);
        }
        foreach (static::TEMPLATE_KEYS as $t) {
            unset($data[static::templateKey($t)]);
        }
        $data['content'] = $content;

        return $data;
    }

    /**
     * Toggles that are ON when the value is missing in older content
     * (a Toggle would otherwise show OFF and hide the section on save).
     */
    public static function withDefaults(array $content): array
    {
        foreach (['hero_show', 'filter_show'] as $key) {
            $content[$key] ??= true;
        }
        foreach (['blocks', 'extra_blocks'] as $list) {
            if (! isset($content[$list]) || ! is_array($content[$list])) {
                continue;
            }
            foreach ($content[$list] as $i => $block) {
                if (! is_array($block)) {
                    continue;
                }
                $data = is_array($block['data'] ?? null) ? $block['data'] : [];
                foreach (['show', 'show_contact', 'form_show', 'map_show', 'wrap', 'show_call', 'show_wa', 'help_show', 'boxes_show', 'space_top'] as $key) {
                    $data[$key] ??= true;
                }
                $content[$list][$i]['data'] = $data;
            }
        }

        return $content;
    }

    /** "Live preview" button: shows the page with the CURRENT (unsaved) form values. */
    public static function previewAction(): Action
    {
        return Action::make('preview')->label('Live preview')->icon(Heroicon::OutlinedEye)->color('info')
            ->slideOver()->modalWidth('7xl')->modalHeading('Live preview')
            ->modalSubmitAction(false)->modalCancelActionLabel('Close')
            ->modalContent(fn ($livewire) => view('filament.page-preview', [
                'html' => static::previewHtml($livewire, $livewire->record ?? null),
            ]));
    }

    /** Full website HTML of the current form values (for the preview iframe). */
    public static function previewHtml($livewire, ?Page $record): string
    {
        try {
            $html = \App\Services\SiteRenderer::render(static::livePage($livewire, $record), true);
        } catch (\Throwable $e) {
            return '<body style="font-family:sans-serif;padding:24px"><h3>Preview could not be built</h3><p>'.e($e->getMessage()).'</p></body>';
        }
        $base = '<base href="'.e(rtrim(url('/'), '/')).'/" target="_blank">';

        return preg_replace('/<head[^>]*>/i', '$0'.$base, $html, 1) ?: $html;
    }

    /** An unsaved Page object built from the current form values. */
    protected static function livePage($livewire, ?Page $record): Page
    {
        try {
            $state = $livewire->getSchema('form')->getStateSnapshot();
        } catch (\Throwable) {
            $state = [];
        }
        $state = static::normalize($state);
        $state = static::mergeContent($state, $state['template'] ?? $record?->template);

        $page = new Page;
        $page->forceFill([
            'title' => $state['title'] ?? $record?->title ?? 'Page',
            'slug' => $state['slug'] ?? $record?->slug ?? 'page',
            'template' => $state['template'] ?? $record?->template ?? 'builder',
            'content' => $state['content'] ?? $record?->content ?? [],
            'type' => in_array($state['template'] ?? $record?->template, ['post', 'landing'], true) ? ($state['template'] ?? $record?->template) : 'page',
            'category_id' => $state['category_id'] ?? $record?->category_id,
            'excerpt' => $state['excerpt'] ?? $record?->excerpt,
            'featured_image' => $state['featured_image'] ?? $record?->featured_image,
            'published_at' => $state['published_at'] ?? $record?->published_at,
        ]);
        foreach (\App\Services\SiteRenderer::SEO_KEYS as $k) {
            $page->{$k} = array_key_exists($k, $state) ? $state[$k] : $record?->{$k};
        }

        return $page;
    }

    /** SEO analysis of the CURRENT (unsaved) form values. */
    public static function liveAnalysis($livewire, ?Page $record): array
    {
        return SeoAnalyzer::analyze(static::livePage($livewire, $record));
    }

    /** Turns upload-field states ([uuid => 'path']) into plain values. */
    protected static function normalize(mixed $v): mixed
    {
        if (is_object($v)) {
            return '';
        }
        if (! is_array($v)) {
            return $v;
        }
        if (count($v) === 1) {
            $key = array_key_first($v);
            $val = $v[$key];
            if (is_string($key) && Str::isUuid($key) && (is_string($val) || is_object($val))) {
                return is_object($val) ? '' : $val;
            }
        }
        if ($v === []) {
            return $v;
        }

        return array_map(fn ($x) => static::normalize($x), $v);
    }
}
