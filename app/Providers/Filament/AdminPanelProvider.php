<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AdminUpcomingSchedules;
use App\Filament\Widgets\MyUpcomingSchedules;
use App\Filament\Widgets\WelcomeHero;
use App\Filament\Widgets\OpenUpcomingSchedules;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
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
            ->profile(isSimple: false)
            ->colors([
                'primary' => [50=>'#eef5f1',100=>'#dcebe3',200=>'#bcd8c9',300=>'#8fbca5',400=>'#5e9b7e',500=>'#3c7d62',600=>'#2c624d',700=>'#245040',800=>'#1f4a3d',900=>'#1a3d32',950=>'#0e2219'],
            ])
            ->darkMode(false)
            ->brandName('Prodiakon')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('7xl')
            ->font('Inter')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                WelcomeHero::class,
                MyUpcomingSchedules::class,
                OpenUpcomingSchedules::class,
                AdminUpcomingSchedules::class,
            ])
            ->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn () => view('components.announcement-banner')
            )
            ->renderHook(
                PanelsRenderHook::LAYOUT_START,
                fn () => view('components.announcement-banner')
            )
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
            ])
            ->renderHook(
                'head.end',
                fn () => view('partials.ga')
            )
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.partials.fonts'))
            ->renderHook(PanelsRenderHook::SIDEBAR_FOOTER, fn () => view('filament.partials.sidebar-footer'))
            ->renderHook(PanelsRenderHook::FOOTER, fn () => view('filament.partials.footer'))
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
