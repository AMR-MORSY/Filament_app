<?php

namespace App\Providers;

use App\Models\Patient;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
        $routeName = $notifiable instanceof Patient
            ? 'patient.password.reset'
            : 'password.reset';

        return url(route($routeName, [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    });
    }
}
