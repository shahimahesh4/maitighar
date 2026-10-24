<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Services\CommunicationService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\UserMenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('stnapanel')
            ->login()
            ->emailVerification(
                isRequired: app(CommunicationService::class)->emailVerificationEnabled(),
            )
            ->brandName('Maitighar')
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('favicon.ico'))
            ->userMenuItems([
                'profile' => UserMenuItem::make()
                    ->url(fn (): string => EditProfile::getUrl()),
                'change_password' => UserMenuItem::make()
                    ->label('Change Password')
                    ->icon('heroicon-o-key')
                    ->url(fn (): string => EditProfile::getUrl()),
                'website' => UserMenuItem::make()
                    ->label('Visit Website')
                    ->icon('heroicon-o-globe-alt')
                    ->url(fn (): string => url('/'), shouldOpenInNewTab: true),
            ])
            ->colors([
                'primary' => Color::hex('#EC0205'),
                'info' => Color::hex('#0C1E8C'),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('User')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Shop')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Settings')
                    ->collapsible(false),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                OrderStats::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn () => view('filament.components.topbar-website-link'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): HtmlString => new HtmlString('
                <style>
                    :root {
                        --maitighar-red: #EC0205;
                        --maitighar-red-dark: #C90204;
                        --maitighar-blue: #0C1E8C;
                        --maitighar-blue-dark: #071355;
                        --maitighar-bg: #f8fafc;
                    }
                    .fi-body { background: #f8fafc; }
                    .fi-sidebar {
                        background: linear-gradient(180deg, #091b38 0%, #071355 100%);
                    }
                    .fi-sidebar-header {
                        background: #EB0302 !important;
                        background-color: #EB0302 !important;
                        border-bottom: 2px solid #C90204 !important;
                    }
                    .fi-sidebar-header button,
                    .fi-sidebar-header button svg,
                    .fi-sidebar-header .fi-icon-btn,
                    .fi-sidebar-header .fi-icon-btn svg {
                        color: #ffffff !important;
                    }
                    .fi-topbar nav {
                        background-color: #ffffff;
                        border-bottom: 2px solid #EB0302;
                    }
                    .fi-sidebar-group-label,
                    .fi-sidebar-item-label,
                    .fi-sidebar-item-icon { color: #e2e8f0; }
                    .fi-sidebar-item > a:hover {
                        background-color: rgba(236, 2, 5, 0.22) !important;
                    }
                    .fi-sidebar-item > a:hover .fi-sidebar-item-label,
                    .fi-sidebar-item > a:hover .fi-sidebar-item-icon {
                        color: #ffffff !important;
                    }
                    .fi-sidebar-item-active > a {
                        background: linear-gradient(135deg, #EC0205, #C90204) !important;
                        box-shadow: 0 4px 14px rgba(236, 2, 5, 0.35);
                    }
                    .fi-sidebar-item-active .fi-sidebar-item-label,
                    .fi-sidebar-item-active .fi-sidebar-item-icon {
                        color: #ffffff !important;
                        font-weight: 600;
                    }
                    .fi-btn-color-primary {
                        background: linear-gradient(135deg, var(--maitighar-red), var(--maitighar-red-dark));
                    }
                    /* Website link button in topbar */
                    .fi-topbar-website-btn {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.375rem;
                        padding: 0.375rem 0.75rem;
                        font-size: 0.8125rem;
                        font-weight: 600;
                        line-height: 1.25rem;
                        color: #334155;
                        background-color: #ffffff;
                        border: 1px solid #cbd5e1;
                        border-radius: 0.5rem;
                        text-decoration: none !important;
                        transition: all 0.2s ease-in-out;
                        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                    }
                    .fi-topbar-website-btn .fi-topbar-website-icon {
                        width: 1rem;
                        height: 1rem;
                        color: #EC0205;
                        transition: color 0.2s ease;
                    }
                    .fi-topbar-website-btn .fi-topbar-website-ext {
                        width: 0.75rem;
                        height: 0.75rem;
                        color: #94a3b8;
                        transition: color 0.2s ease;
                    }
                    .fi-topbar-website-btn:hover {
                        background-color: #fef2f2;
                        border-color: #EC0205;
                        color: #EC0205;
                        box-shadow: 0 2px 6px rgba(236, 2, 5, 0.15);
                    }
                    .fi-topbar-website-btn:hover .fi-topbar-website-icon,
                    .fi-topbar-website-btn:hover .fi-topbar-website-ext {
                        color: #EC0205;
                    }
                    .fi-topbar-website-btn:active {
                        transform: scale(0.97);
                    }
                    .dark .fi-topbar-website-btn {
                        background-color: #1e293b;
                        border-color: #334155;
                        color: #f1f5f9;
                    }
                    .dark .fi-topbar-website-btn:hover {
                        background-color: rgba(236, 2, 5, 0.15);
                        border-color: #EC0205;
                        color: #ffffff;
                    }
                    /* Hide scrollbar for Chrome, Safari and Opera */
                    .fi-sidebar-nav::-webkit-scrollbar,
                    .fi-sidebar::-webkit-scrollbar,
                    .fi-sidebar-nav-container::-webkit-scrollbar {
                        display: none !important;
                    }
                    /* Hide scrollbar for IE, Edge and Firefox */
                    .fi-sidebar-nav,
                    .fi-sidebar,
                    .fi-sidebar-nav-container {
                        -ms-overflow-style: none !important;  /* IE and Edge */
                        scrollbar-width: none !important;  /* Firefox */
                    }
                </style>
            '),
        );
    }
}
