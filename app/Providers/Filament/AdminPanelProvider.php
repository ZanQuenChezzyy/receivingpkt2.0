<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\EditProfile;
use App\Filament\Pages\Login;
use Caresome\FilamentAuthDesigner\AuthDesignerPlugin;
use Caresome\FilamentAuthDesigner\Enums\MediaPosition;
use Caresome\FilamentNeobrutalism\NeobrutalismeTheme;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Enums\ThemeMode;
use Filament\Enums\UserMenuPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// use WatheqAlshowaiter\FilamentStickyTableHeader\StickyTableHeaderPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $themePlugin = $this->themePlugin();

        return $panel
            ->default()
            ->spa()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->profile(EditProfile::class)
            ->brandLogo('/images/logo/receiving_dark.png')
            ->darkModeBrandLogo('/images/logo/receiving_white.png')
            ->brandLogoHeight('2.5rem')
            ->defaultThemeMode(ThemeMode::System)
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Cyan,
                'primary' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->databaseNotifications()
            ->userMenu(position: UserMenuPosition::Topbar)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->when(
                $themePlugin,
                fn(Panel $panel) => $panel->plugins([$themePlugin])
            )
            // FOOTER PLUGIN
            ->plugins([
                EasyFooterPlugin::make()
                    ->withBorder()
                    ->withSentence('LaravelChezzy. All rights reserved.'),
                AuthDesignerPlugin::make()
                    ->defaults(
                        fn($config) => $config
                            ->media(asset('images/auth/background-auth.png'))
                            ->mediaPosition(MediaPosition::Left)
                            ->blur(0)
                            ->mediasize('70%')
                    )
                    ->themeToggle(),
                // StickyTableHeaderPlugin::make()
                //     ->shouldScrollToTopOnPageChanged(enabled: true, behavior: "smooth"),
            ])
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

    private function themePlugin(): ?object
    {
        return match (config('chezzy.active_theme')) {
            'neobrutalism' => NeobrutalismeTheme::make()
                ->customize([
                    'border-width' => '2px',
                    'shadow-offset-md' => '3px',
                    'radius-md' => '0.5rem',
                ]),

            default => null,
        };
    }
}
