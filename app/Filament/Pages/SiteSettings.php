<?php

namespace App\Filament\Pages;

use App\Filament\Support\SiteFields as F;
use App\Models\Page as SitePage;
use App\Models\Setting;
use App\Services\SiteMailer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Site Settings';

    protected static ?string $slug = 'site-settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $all = Setting::allSettings();
        $all['smtp_password'] = '';
        $this->form->fill($all);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Tabs::make('settings')->persistTabInQueryString()->vertical()->tabs([
                $this->businessTab(),
                $this->contactTab(),
                $this->headerTab(),
                $this->footerTab(),
                $this->designTab(),
                $this->cardStyleTab(),
                $this->landingTab(),
                $this->popupTab(),
                $this->floatingTab(),
                $this->formTab(),
                $this->emailTab(),
                $this->trackingTab(),
                $this->seoTab(),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save settings')->submit('save')->keyBindings(['mod+s']),
                    ])->sticky(),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::putMany($data);
        $this->data['smtp_password'] = '';

        Notification::make()->title('Settings saved')->body('The website is updated everywhere.')->success()->send();
    }

    /* ---------------------------------------------------------------- */
    protected function pageLinks(): array
    {
        try {
            return SitePage::query()->orderBy('title')->pluck('slug')->map(fn ($s) => $s === 'home' ? '' : $s)->push('popup')->all();
        } catch (\Throwable) {
            return [];
        }
    }

    protected function links(string $name, string $label): Repeater
    {
        return Repeater::make($name)->label($label)
            ->schema([
                TextInput::make('label')->label('Text')->required()->maxLength(60),
                TextInput::make('link')->label('Link')->datalist($this->pageLinks())->maxLength(255)
                    ->helperText('Page slug (empty = Home), or full https:// link'),
            ])->columns(2)->reorderable()->defaultItems(0)->addActionLabel('Add link')
            ->itemLabel(fn (array $state) => $state['label'] ?? null)->collapsible();
    }

    protected function businessTab(): Tab
    {
        return Tab::make('Business')->icon(Heroicon::OutlinedBuildingStorefront)->schema([
            Section::make('Business details')->columns(2)->schema([
                TextInput::make('business_name')->label('Business name')->required()->maxLength(100)
                    ->helperText('Used in page titles, emails and wherever {business} is written.'),
                TextInput::make('legal_name')->label('Registered company name (optional)')->placeholder('SM Cab Service Pvt Ltd')->maxLength(150)
                    ->helperText('Shown in the Trust Factor box. Empty = business name.'),
                TextInput::make('logo_text')->label('Text logo (used when no logo image)')->placeholder('SM[Cab Service]')->maxLength(60)
                    ->helperText('Words in [square brackets] show in yellow.'),
                TextInput::make('tagline')->label('Tagline (shown next to the logo only when the name is shown)')->maxLength(120),
                Toggle::make('show_name_with_logo')->label('Show business name next to the logo')->inline(false),
                TextInput::make('website_url')->label('Website address (optional)')->url()->placeholder('https://www.yourdomain.com')
                    ->helperText('Leave empty to detect automatically.'),
            ]),
            Section::make('Logo & icons')->columns(3)->schema([
                F::image('logo', 'general', 'Logo (wide, e.g. 276 × 124 WebP/PNG)'),
                F::image('favicon', 'general', 'Browser tab icon (favicon) — empty = logo'),
                F::image('apple_touch_icon', 'general', 'Phone home-screen icon (180 × 180, optional)'),
            ]),
        ]);
    }

    protected function contactTab(): Tab
    {
        return Tab::make('Phone, WhatsApp & Email')->icon(Heroicon::OutlinedPhone)->schema([
            Section::make('Contact details')->description('Change once — updates the top bar, header, footer, contact page, every Call / WhatsApp button and every {phone} in texts.')->columns(2)->schema([
                TextInput::make('mobile')->label('Phone number (as shown on site)')->required()->maxLength(30)->placeholder('+91 83830 51919'),
                TextInput::make('whatsapp_number')->label('WhatsApp number (with country code)')->required()->maxLength(20)
                    ->placeholder('918383051919')->helperText('91 + 10-digit number. No + or spaces.')
                    ->regex('/^[0-9]{10,15}$/'),
                TextInput::make('mobile_2')->label('Second phone number (optional)')->maxLength(30)->placeholder('+91 87090 88100'),
                TextInput::make('email')->label('Email shown on site')->email()->maxLength(150),
                TextInput::make('working_hours')->label('Working hours (optional)')->maxLength(100)->placeholder('Mon–Sun, 8 AM – 9 PM'),
                Textarea::make('address')->label('Address')->rows(3)->columnSpanFull(),
            ]),
            Section::make('WhatsApp ready messages')->description('Text already typed when the customer opens WhatsApp.')->schema([
                Textarea::make('whatsapp_message')->label('General WhatsApp buttons')->rows(2),
                Textarea::make('whatsapp_card_message')->label('WhatsApp button on cab cards')->rows(2)->helperText('{item} = cab name, e.g. "Toyota Innova"'),
            ]),
        ]);
    }

    protected function headerTab(): Tab
    {
        return Tab::make('Header & Menu')->icon(Heroicon::OutlinedBars3)->schema([
            Section::make('Thin top bar')->columns(3)->schema([
                Toggle::make('topbar_show')->label('Show top bar')->inline(false),
                TextInput::make('topbar_text')->label('Text (with green dot)')->columnSpan(2),
                Toggle::make('topbar_email_show')->label('Show email')->inline(false),
                Toggle::make('topbar_hours_show')->label('Show working hours')->inline(false),
                Toggle::make('topbar_phone_show')->label('Show phone on the right')->inline(false),
                TextInput::make('topbar_phone_label')->label('Word before phone')->placeholder('Call:'),
            ]),
            Section::make('Header buttons')->columns(2)->schema([
                Toggle::make('header_wa_show')->label('Show WhatsApp button')->inline(false),
                TextInput::make('header_wa_text')->label('WhatsApp button text')->placeholder('WhatsApp'),
                Toggle::make('header_phone_show')->label('Show yellow Call button')->inline(false),
                TextInput::make('header_phone_label')->label('Call button text')->placeholder('Call Now'),
                Toggle::make('header_button_show')->label('Show Book button (opens booking popup)')->inline(false),
                TextInput::make('header_button_text')->label('Book button text'),
            ]),
            Section::make('Main menu')->description('Shown on every page.')->schema([$this->links('menu', 'Menu links')]),
            Section::make('Home page menu (optional)')->description('The home page can have its own menu that scrolls to sections, e.g. #services, #how, #why, #reviews, #faq. Empty = main menu.')
                ->collapsible()->schema([$this->links('home_menu', 'Home page menu links')]),
            Section::make('Inner page banner')->schema([
                F::image('inner_hero_image', 'banners', 'Default banner photo of inner pages (About, Contact, Privacy …)'),
            ]),
        ]);
    }

    protected function footerTab(): Tab
    {
        return Tab::make('Footer')->icon(Heroicon::OutlinedQueueList)->schema([
            Section::make('Under the logo')->schema([
                Textarea::make('footer_about')->label('About text')->rows(3)->helperText('Also used as the business description for Google.'),
                Textarea::make('footer_disclaimer')->label('Small disclaimer')->rows(2),
            ]),
            Section::make('Columns')->columns(2)->schema([
                TextInput::make('footer_quick_title')->label('First links column – title'),
                TextInput::make('footer_info_title')->label('Second links column – title'),
                $this->links('footer_quick_links', 'First links column – links'),
                $this->links('footer_info_links', 'Second links column – links'),
                TextInput::make('footer_contact_title')->label('Contact column – title'),
                Toggle::make('footer_hours_show')->label('Show working hours in contact column')->inline(false),
            ]),
            Section::make('Popular searches (keyword links above the bottom line)')->description('Small round links to your service pages — good for Google. Empty = row hidden.')->collapsible()->schema([
                TextInput::make('footer_tags_title')->label('Title')->placeholder('Popular Searches'),
                $this->links('footer_tags', 'Links'),
            ]),
            Section::make('Bottom line')->columns(2)->schema([
                TextInput::make('copyright')->label('Copyright line')->helperText('{year} = current year · {business} = business name')->columnSpanFull(),
                TextInput::make('footer_credit_text')->label('Small text on the right (optional)')->placeholder('Car photos for illustration · Photo credits')
                    ->helperText('Needed while the cab cards use Wikimedia photos (their licence asks for credit). Empty = hidden.'),
                TextInput::make('footer_credit_link')->label('It opens (page address)')->placeholder('photo-credits.php'),
            ]),
            Section::make('Social media')->description('Paste the full link. Empty = icon hidden.')->columns(2)->schema([
                TextInput::make('social.facebook')->label('Facebook')->url(),
                TextInput::make('social.instagram')->label('Instagram')->url(),
                TextInput::make('social.youtube')->label('YouTube')->url(),
                TextInput::make('social.linkedin')->label('LinkedIn')->url(),
                TextInput::make('social.x')->label('X (Twitter)')->url(),
            ]),
        ]);
    }

    protected function designTab(): Tab
    {
        $btn = fn (string $key, string $label, bool $text = true) => Grid::make(3)->schema(array_values(array_filter([
            $text ? TextInput::make("buttons.{$key}_text")->label($label.' – text') : Text::make($label)->columnSpan(1),
            ColorPicker::make("buttons.{$key}_bg")->label('Background'),
            ColorPicker::make("buttons.{$key}_color")->label('Text colour'),
        ])));

        return Tab::make('Colours & Buttons')->icon(Heroicon::OutlinedSwatch)->schema([
            Section::make('Brand colours')->description('The whole design follows these colours.')->columns(4)->schema([
                ColorPicker::make('colors.primary')->label('Primary (blue)'),
                ColorPicker::make('colors.dark')->label('Dark (navy)'),
                ColorPicker::make('colors.accent')->label('Highlight (orange)'),
                ColorPicker::make('colors.light')->label('Light section background'),
            ]),
            Section::make('Buttons')->schema([
                $btn('main', 'Orange "Book" buttons', false),
                $btn('call', 'Call button'),
                $btn('whatsapp', 'WhatsApp button'),
                $btn('submit', 'Form button (on pages)'),
                $btn('popup', 'Form button (in popup)'),
                Grid::make(3)->schema([
                    TextInput::make('buttons.sending_text')->label('Text while form is sending'),
                    TextInput::make('buttons.more_text')->label('Link text on appliance cards')->placeholder('View service →'),
                ]),
            ]),
        ]);
    }

    /** Master style for every service card. */
    protected function cardStyleTab(): Tab
    {
        $color = fn (string $key, string $label) => ColorPicker::make("card.{$key}")->label($label)->helperText('Empty = original');

        return Tab::make('Service Card Style')->icon(Heroicon::OutlinedRectangleGroup)->schema([
            Section::make('Master style for all service cards')
                ->description('Change it here once — every service card on the website follows it. Colours left empty keep the original design.')
                ->headerActions([
                    Action::make('previewCards')->label('Preview cards')->icon(Heroicon::OutlinedEye)->color('info')
                        ->slideOver()->modalWidth('7xl')->modalHeading('Service cards preview (unsaved settings)')
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close')
                        ->modalContent(fn ($livewire) => view('filament.page-preview', ['html' => $this->cardPreviewHtml()])),
                ])->columns(3)->schema([
                    TextInput::make('card.radius')->label('Card corner roundness')->numeric()->minValue(0)->maxValue(60)->suffix('px')->placeholder('20'),
                    TextInput::make('card.filter_all_text')->label('"All" filter button text')->placeholder('All'),
                    Text::make(' '),
                    $color('badge_bg', 'Ribbon on picture – background'),
                    $color('badge_color', 'Ribbon – text colour'),
                    Text::make(' '),
                    $color('label_bg', 'Tag next to cab name – background'),
                    $color('label_color', 'Tag – text colour'),
                ]),
            Section::make('Buttons on the card')->columns(3)->schema([
                TextInput::make('buttons.card_book_text')->label('Book button text')->placeholder('Book Now'),
                TextInput::make('buttons.card_call_text')->label('Call icon – label for screen readers')->placeholder('Call'),
                TextInput::make('buttons.card_whatsapp_text')->label('WhatsApp icon – label for screen readers')->placeholder('WhatsApp'),
                Toggle::make('buttons.card_show_call')->label('Show Call icon')->inline(false),
                Toggle::make('buttons.card_show_whatsapp')->label('Show WhatsApp icon')->inline(false),
            ]),
            Section::make('Fare row')->columns(3)->schema([
                TextInput::make('card.price_label')->label('Small word above fare (when the cab has none)')->placeholder('Starting'),
                TextInput::make('card.request_text')->label('"Get Quote" big text')->placeholder('Get Quote'),
                TextInput::make('card.request_note')->label('"Get Quote" small text (popup / email)')->placeholder('Fare on call'),
            ]),
        ]);
    }

    /** Service cards rendered with the settings currently typed in the form (not saved yet). */
    protected function cardPreviewHtml(): string
    {
        try {
            $state = $this->data ?? [];
        } catch (\Throwable) {
            $state = $this->data ?? [];
        }
        $settings = array_replace_recursive(Setting::allSettings(), array_filter([
            'card' => $state['card'] ?? null,
            'buttons' => $state['buttons'] ?? null,
        ], fn ($v) => is_array($v)));

        try {
            $html = \App\Services\SiteRenderer::renderBlockPreview('services', ['limit' => 3], $settings, true);
        } catch (\Throwable $e) {
            return '<body style="font-family:sans-serif;padding:24px">'.e($e->getMessage()).'</body>';
        }

        return $html;
    }

    protected function popupTab(): Tab
    {
        return Tab::make('Popup, Banner & Thank You')->icon(Heroicon::OutlinedWindow)->schema([
            Section::make('Booking popup')->description('Opens from every Book button. The line under the heading shows the cab or route that was clicked.')->columns(2)->schema([
                TextInput::make('popup_heading')->label('Heading')->placeholder('Book Your Cab'),
                TextInput::make('popup_small')->label('Line under heading (when nothing is selected)')->placeholder('Get fare & confirmation on WhatsApp'),
            ]),
            Section::make('Yellow call-to-action band (default text)')->columns(2)->schema([
                TextInput::make('cta_heading')->label('Heading'),
                TextInput::make('cta_text')->label('Text'),
                TextInput::make('cta_call_text')->label('Call button text'),
                TextInput::make('cta_wa_text')->label('WhatsApp button text')->helperText('Write - to hide'),
                TextInput::make('cta_book_text')->label('Book button text (opens popup)')->helperText('Write - to hide'),
                F::image('cta_image', 'banners', 'Faint background photo (optional)'),
            ]),
            Section::make('Thank-you page buttons')->columns(3)->schema([
                TextInput::make('thankyou.call_text')->label('Call button text'),
                ColorPicker::make('thankyou.call_bg')->label('Call button colour'),
                ColorPicker::make('thankyou.call_color')->label('Call button text colour'),
                TextInput::make('thankyou.wa_message')->label('WhatsApp message')->columnSpan(2),
                TextInput::make('thankyou.home_text')->label('Home button text'),
            ]),
            Section::make('Page not found (404)')->columns(2)->schema([
                TextInput::make('notfound_title')->label('Heading'),
                TextInput::make('notfound_text')->label('Line under heading'),
                TextInput::make('notfound_body')->label('Text'),
                TextInput::make('notfound_button')->label('Button text'),
            ]),
        ]);
    }

    protected function floatingTab(): Tab
    {
        return Tab::make('Floating Buttons')->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)->schema([
            Section::make('Floating Call & WhatsApp buttons')->columns(3)->schema([
                Toggle::make('floating.floating_enabled')->label('Show floating buttons')->inline(false),
                Toggle::make('floating.call_enabled')->label('Call button')->inline(false),
                Toggle::make('floating.whatsapp_enabled')->label('WhatsApp button')->inline(false),
                Select::make('floating.position_side')->label('Side')->options(['right' => 'Right', 'left' => 'Left'])->selectablePlaceholder(false),
                Select::make('floating.layout')->label('Layout')->options(['vertical' => 'One above the other', 'horizontal' => 'Side by side'])->selectablePlaceholder(false),
                Select::make('floating.animation')->label('Animation')->options(['none' => 'None', 'pulse' => 'Pulse', 'blink' => 'Blink', 'shake' => 'Shake', 'bounce' => 'Bounce', 'ring' => 'Ring'])->selectablePlaceholder(false),
                Select::make('floating.animation_speed')->label('Animation speed')->options(['slow' => 'Slow', 'normal' => 'Normal', 'fast' => 'Fast'])->selectablePlaceholder(false),
                TextInput::make('floating.position_x')->label('Distance from side (px)')->numeric(),
                TextInput::make('floating.position_y')->label('Distance from bottom (px)')->numeric(),
                TextInput::make('floating.gap')->label('Gap between buttons (px)')->numeric(),
                TextInput::make('floating.size')->label('Size on computer (px)')->numeric(),
                TextInput::make('floating.mobile_size')->label('Size on mobile (px)')->numeric(),
                TextInput::make('floating.icon_size')->label('Icon size (px)')->numeric(),
                ColorPicker::make('floating.call_color')->label('Call colour'),
                ColorPicker::make('floating.whatsapp_color')->label('WhatsApp colour'),
                Toggle::make('floating.show_on_mobile')->label('Show on mobile')->inline(false),
                Toggle::make('floating.show_on_desktop')->label('Show on computer')->inline(false),
                Toggle::make('floating.tooltip_enabled')->label('Label on mouse-over')->inline(false),
                TextInput::make('floating.call_tooltip_text')->label('Call label'),
                TextInput::make('floating.whatsapp_tooltip_text')->label('WhatsApp label'),
            ]),
            Section::make('Mobile bottom bar (Call | WhatsApp | Book)')->description('Best for Google Ads. Floating buttons move up above the bar on mobile.')->columns(4)->schema([
                Toggle::make('mobile_bar.enabled')->label('Show bar')->inline(false),
                TextInput::make('mobile_bar.call_text')->label('Call text'),
                TextInput::make('mobile_bar.whatsapp_text')->label('WhatsApp text'),
                TextInput::make('mobile_bar.main_text')->label('Book button text'),
            ]),
        ]);
    }

    protected function formTab(): Tab
    {
        return Tab::make('Booking Form')->icon(Heroicon::OutlinedClipboardDocumentList)->schema([
            Section::make('Form fields (used by ALL forms)')->schema([
                Repeater::make('form_fields')->label('Fields')
                    ->schema([
                        TextInput::make('label')->label('Label (shown above box)')->required(),
                        TextInput::make('name')->label('Code (no spaces)')->required()->regex('/^[a-z0-9_]+$/i')
                            ->helperText('Keep "name" and "mobile" as they are.'),
                        Select::make('type')->options([
                            'trip' => 'Trip type tabs (One Way / Round Trip …)', 'text' => 'Text', 'tel' => 'Mobile number (+91)', 'email' => 'Email',
                            'date' => 'Date', 'time' => 'Time', 'number' => 'Number', 'select' => 'Dropdown', 'textarea' => 'Big text box',
                        ])->default('text')->required()->live(),
                        TextInput::make('placeholder')->label('Hint inside box (dropdown: first line)')
                            ->visible(fn ($get) => ! in_array($get('type'), ['trip', 'date', 'time'], true)),
                        TextInput::make('error')->label('Error message')->placeholder('Please fill this field'),
                        Textarea::make('options')->label(fn ($get) => $get('type') === 'trip' ? 'Trip types (one per line — first one is selected)' : 'Dropdown options')->rows(4)
                            ->helperText(fn ($get) => $get('type') === 'trip' ? 'Shown as tabs at the top of every booking form.' : 'Write "services" for the cab list (Cabs & Fares), or one option per line.')
                            ->visible(fn ($get) => in_array($get('type'), ['select', 'trip'], true)),
                        TextInput::make('min')->label('Min. letters / number')->numeric()->visible(fn ($get) => in_array($get('type'), ['text', 'textarea', 'number'], true)),
                        TextInput::make('max')->label('Max. letters / number')->numeric()->visible(fn ($get) => in_array($get('type'), ['text', 'textarea', 'number'], true)),
                        TextInput::make('show_for')->label('Show only for these trip types (optional)')->placeholder('Round Trip')
                            ->helperText('Comma separated, e.g. "One Way, Round Trip, Airport". Empty = always shown.')
                            ->visible(fn ($get) => $get('type') !== 'trip'),
                        TextInput::make('default')->label('Starting value (optional)')
                            ->helperText(fn ($get) => match ($get('type')) { 'date' => 'Write "today" to fill today\'s date.', 'time' => 'e.g. 09:00', default => '' })
                            ->visible(fn ($get) => in_array($get('type'), ['text', 'date', 'time'], true)),
                        F::icon('icon', 'Icon inside the box (optional)')->visible(fn ($get) => in_array($get('type'), ['text', 'select'], true)),
                        Toggle::make('half')->label('Half width (two fields side by side)')->inline(false)->visible(fn ($get) => $get('type') !== 'trip'),
                        Toggle::make('required')->label('Must fill')->inline(false),
                        Toggle::make('short_form')->label('Show in the popup form')->default(true)->inline(false),
                    ])->columns(3)->reorderable()->addActionLabel('Add field')
                    ->itemLabel(fn (array $state) => ($state['label'] ?? '').(! empty($state['required']) ? ' *' : ''))->collapsible()->collapsed(),
                TextInput::make('privacy_note')->label('Small line under every form (with lock icon)'),
                Grid::make(2)->schema([
                    TextInput::make('form_error_text')->label('Message when fields are wrong'),
                    TextInput::make('form_offline_text')->label('Message when there is no internet'),
                ]),
            ]),

            Section::make('After the form is sent')
                ->description('Choose what happens when a visitor sends any form (banner, popup, contact page …). Every enquiry is always saved under Enquiries (Leads).')
                ->columns(2)->schema([
                    Toggle::make('form_action.send_email')->label('Email me every enquiry (needs the Email / SMTP tab)')->inline(false)->default(true),
                    Select::make('form_action.after_submit')->label('Then take the visitor to')
                        ->options([
                            'thankyou' => 'Thank-you page',
                            'whatsapp' => 'WhatsApp — opens chat with all the form details filled in',
                        ])->default('thankyou')->selectablePlaceholder(false)->live(),
                    Toggle::make('form_action.thankyou_wa_share')->label('Thank-you page: show "Share details on WhatsApp" button')->inline(false)
                        ->visible(fn ($get) => $get('form_action.after_submit') !== 'whatsapp'),
                    TextInput::make('form_action.share_text')->label('Share button text')->placeholder('Share details on WhatsApp')
                        ->visible(fn ($get) => $get('form_action.after_submit') !== 'whatsapp'),
                    TextInput::make('form_action.wa_number')->label('WhatsApp number to send details to')->placeholder('Empty = your WhatsApp number from Phone, WhatsApp & Email'),
                    Textarea::make('form_action.wa_template')->label('WhatsApp message')->rows(5)->columnSpanFull()
                        ->helperText('{details} = all form fields · {name} · {mobile} · {item} · {for} (" about Service") · {page} · {business}'),
                    Toggle::make('form_action.datalayer_data')->label('Send the form values to Google Tag Manager (dataLayer)')->inline(false)
                        ->helperText('Off = only the event (form_submit / whatsapp_form_submit) with form name and service. On = also the typed values (for Enhanced Conversions).'),
                ]),
        ]);
    }

    /** Header, footer and mobile bar used by every Landing Page. */
    protected function landingTab(): Tab
    {
        $color = fn (string $key, string $label) => ColorPicker::make("landing.{$key}")->label($label)->helperText('Empty = default');

        return Tab::make('Landing Pages')->icon(Heroicon::OutlinedRocketLaunch)->schema([
            Section::make('Header (logo + buttons only — no menu)')
                ->description('Landing pages use their own header. The logo is not a link, and no link inside the page can take the visitor away.')
                ->columns(3)->schema([
                    TextInput::make('landing.call_text')->label('Call button text')->placeholder('Call Now'),
                    Toggle::make('landing.call_show_number')->label('Show the number on the button')->inline(false),
                    Toggle::make('landing.sticky')->label('Keep header fixed while scrolling')->inline(false),
                    Toggle::make('landing.call_desktop')->label('Call button — on computer')->inline(false),
                    Toggle::make('landing.call_mobile')->label('Call button — on mobile')->inline(false),
                    TextInput::make('landing.enq_text')->label('Book button text')->placeholder('Book Now'),
                    Toggle::make('landing.enq_desktop')->label('Enquiry button — on computer')->inline(false),
                    Toggle::make('landing.enq_mobile')->label('Enquiry button — on mobile')->inline(false),
                    $color('header_bg', 'Header background'),
                    $color('header_color', 'Header text colour'),
                ]),
            Section::make('Footer')->columns(3)->schema([
                Toggle::make('landing.footer_show')->label('Show footer')->inline(false),
                $color('footer_bg', 'Footer background'),
                $color('footer_color', 'Footer text colour'),
                TextInput::make('landing.footer_text')->label('Footer line')->helperText('{year} and {business} work here')->columnSpanFull(),
                Textarea::make('landing.footer_note')->label('Small disclaimer (optional)')->rows(2)->columnSpanFull(),
            ]),
            Section::make('Bottom bar on mobile')->description('Great for Google Ads — Call and Enquire are always one tap away.')->columns(4)->schema([
                Toggle::make('landing.mobile_bar')->label('Show bottom bar')->inline(false),
                Toggle::make('landing.mb_call')->label('Call')->inline(false),
                Toggle::make('landing.mb_wa')->label('WhatsApp')->inline(false),
                Toggle::make('landing.mb_enq')->label('Book')->inline(false),
                Toggle::make('landing.floating')->label('Also show the round floating buttons')->inline(false)->columnSpanFull(),
            ]),
        ]);
    }

    protected function emailTab(): Tab
    {
        return Tab::make('Email / SMTP')->icon(Heroicon::OutlinedEnvelope)->schema([
            Section::make('Where enquiries are sent')->columns(2)->schema([
                TextInput::make('mail_to')->label('Send enquiries to')->required()->helperText('More than one? Separate with commas.'),
                TextInput::make('mail_cc')->label('Copy (CC) to (optional)'),
                TextInput::make('buttons.email_action_word')->label('Email subject word')->helperText('"New Booking – Cooling Issue Repair (Refrigerator) – Rahul"'),
            ]),
            Section::make('SMTP (sending server)')
                ->description('Gmail: smtp.gmail.com · port 465 · SSL · password = 16-letter App Password (Google Account → Security → 2-Step Verification → App passwords). Hostinger email: smtp.hostinger.com · port 465 · SSL · normal email password.')
                ->columns(3)
                ->headerActions([
                    Action::make('testEmail')->label('Send test email')->icon(Heroicon::OutlinedPaperAirplane)
                        ->schema([TextInput::make('to')->label('Send test to')->email()->required()->default(fn () => $this->data['mail_to'] ?? auth()->user()?->email)])
                        ->action(fn (array $data) => $this->sendTest($data['to'])),
                ])
                ->schema([
                    Toggle::make('smtp_on')->label('Use SMTP (recommended)')->inline(false)->columnSpanFull(),
                    TextInput::make('smtp_host')->label('SMTP host')->placeholder('smtp.hostinger.com'),
                    TextInput::make('smtp_port')->label('Port')->numeric()->placeholder('465'),
                    Select::make('smtp_secure')->label('Security')->options(['ssl' => 'SSL (port 465)', 'tls' => 'TLS (port 587)', 'none' => 'None'])->selectablePlaceholder(false),
                    TextInput::make('smtp_username')->label('Username (full email)'),
                    TextInput::make('smtp_password')->label('Password')->password()->revealable()
                        ->placeholder(fn () => Setting::get('smtp_password') ? '•••••••• saved — leave empty to keep' : 'Not set yet')
                        ->helperText('Stored encrypted.'),
                    TextInput::make('from_email')->label('From email (optional)')->email()->helperText('Empty = SMTP username'),
                    TextInput::make('from_name')->label('From name'),
                ]),
            Section::make('Auto-reply to customer')->description('Sent only if the customer typed an email. {name} {item} {phone} work here.')->collapsible()->collapsed()->schema([
                Toggle::make('autoreply_on')->label('Send auto-reply'),
                TextInput::make('autoreply_subject')->label('Subject'),
                Textarea::make('autoreply_message')->label('Message')->rows(6),
            ]),
        ]);
    }

    protected function trackingTab(): Tab
    {
        $box = fn (string $key, string $label, string $help) => Section::make($label)->description($help)->collapsible()->schema([
            Toggle::make("tracking.{$key}_code_on")->label('ON'),
            Textarea::make("tracking.{$key}_code")->hiddenLabel()->rows(6)->extraInputAttributes(['style' => 'font-family:monospace;font-size:12px']),
        ]);

        return Tab::make('Tracking Codes')->icon(Heroicon::OutlinedChartBar)->schema([
            Text::make('Paste codes exactly as given by Google / Facebook. Every Call and WhatsApp click also sends "call_click" / "whatsapp_click" events to Google Tag Manager (dataLayer).'),
            $box('head', '1. Head code (every page, inside <head>)', 'Google Tag Manager <head> code, GA4 gtag.js, Meta Pixel.'),
            $box('bodystart', '2. Body start code (right after <body>)', 'Google Tag Manager <noscript> code.'),
            $box('bodyend', '3. Body end code (before </body>)', 'Chat widgets, other scripts.'),
            $box('thankyou', '4. Thank-you page only', 'Google Ads conversion event, Meta Pixel "Lead" event.'),
        ]);
    }

    protected function seoTab(): Tab
    {
        return Tab::make('SEO & Google Business')->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
            Section::make('Local business details (shown to Google)')->columns(3)->schema([
                Select::make('schema_type')->label('Business type')->options([
                    'TaxiService' => 'Taxi / cab service',
                    'LocalBusiness' => 'Local business',
                    'HomeAndConstructionBusiness' => 'Home service business',
                    'ProfessionalService' => 'Professional service',
                    'Organization' => 'Organization',
                ])->selectablePlaceholder(false),
                TextInput::make('schema_street')->label('Street address (optional)'),
                TextInput::make('city')->label('City'),
                TextInput::make('state')->label('State'),
                TextInput::make('pincode')->label('PIN code'),
                TextInput::make('schema_area_served')->label('Area served')->placeholder('Bihar, Jharkhand, Uttar Pradesh'),
                TextInput::make('schema_opening_hours')->label('Opening hours (Google format)')->helperText('e.g. Mo-Su 08:00-21:00 · empty = not shown'),
                TextInput::make('price_range')->label('Price range (optional)')->placeholder('₹₹'),
            ]),
            Section::make('Search console & sharing')->columns(2)->schema([
                TextInput::make('google_site_verification')->label('Google Search Console verification code')->helperText('Only the content="…" value of the meta tag.'),
                TextInput::make('bing_site_verification')->label('Bing verification code'),
                F::image('default_og_image', 'social', 'Default share image (1200 × 630)'),
                Textarea::make('robots_extra')->label('Extra robots.txt lines (advanced)')->rows(4),
            ]),
            Text::make(fn () => 'Sitemap: '.url('sitemap.xml').'  ·  Robots: '.url('robots.txt').'  — submit the sitemap in Google Search Console.'),
        ]);
    }

    protected function sendTest(string $to): void
    {
        $state = array_replace(Setting::allSettings(), $this->form->getRawState());
        if (blank($state['smtp_password'] ?? null)) {
            $state['smtp_password'] = Setting::get('smtp_password');
        }
        try {
            $m = SiteMailer::make($state);
            $m->addAddress($to);
            $m->Subject = 'Test email from '.($state['business_name'] ?? 'your website');
            $m->Body = 'If you can read this, your email (SMTP) settings are working. Enquiries from the website will arrive here.';
            $m->send();
            Notification::make()->title('Test email sent to '.$to)->body('Check the inbox (and spam folder). Remember to SAVE the settings.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Email could not be sent')->body($e->getMessage())->danger()->persistent()->send();
        }
    }
}
