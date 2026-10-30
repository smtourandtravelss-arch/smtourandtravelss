<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(isSimple: false)
            ->brandName(fn () => static::setting('business_name', 'Website').' Admin')
            ->brandLogo(fn () => ($logo = static::setting('logo')) ? url($logo) : null)
            ->brandLogoHeight('2.6rem')
            ->favicon(fn () => ($fav = static::setting('favicon')) ? url($fav) : null)
            ->colors([
                'primary' => Color::hex('#3b6ef5'),
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
            ])
            ->font('Nunito Sans')
            ->sidebarWidth('16rem')
            ->maxContentWidth(Width::Full)
            ->renderHook(
                \Filament\View\PanelsRenderHook::SIDEBAR_LOGO_AFTER,
                fn () => '<span class="hc-brand"><b>'.e(static::setting('business_name', 'Website')).'</b><small>Admin panel</small></span>',
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn () => '<link rel="stylesheet" href="'.e(asset('assets/admin/layout-a.css')).'?v='.@filemtime(public_path('assets/admin/layout-a.css')).'">',
            )
            ->navigationGroups([
                NavigationGroup::make('Website'),
                NavigationGroup::make('Enquiries'),
                NavigationGroup::make('Settings'),
            ])
            ->navigationItems([
                NavigationItem::make('View website')
                    ->url(fn () => url('/'), shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->sort(100),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn () => request()->is('admin/pages*') ? view('filament.block-hover') : '',
            )
            ->databaseNotifications(false)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected static function setting(string $key, ?string $default = null): ?string
    {
        try {
            $v = Setting::get($key, $default);

            return is_string($v) && $v !== '' ? $v : $default;
        } catch (\Throwable) {
            return $default; // before installation there is no database
        }
    }
}
