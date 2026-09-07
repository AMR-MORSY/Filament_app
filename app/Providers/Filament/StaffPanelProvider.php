<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Appointments\AppointmentResource;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class StaffPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('staff')
            ->path('staff')
            ->login()
            ->brandLogo(asset('images/brand/mark.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/brand/mark.svg'))
            ->brandName('Rowan Clinic Staff')
            ->colors([
                'primary' => Color::Sky,
            ])
            ->resources([
                AppointmentResource::class,
                PatientResource::class,
            ])

            ->pages([
                Dashboard::class,
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
}
