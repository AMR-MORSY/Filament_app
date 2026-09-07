<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Models\Patient;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware('guest:patient')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('auth.login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('auth.login.submit');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('auth.register');
    Route::post('/register', [RegisterController::class, 'register'])->name('auth.register.submit');

    /*
    |--------------------------------------------------------------------------
    | Patient password reset  (guard: patient, broker: patients)
    |--------------------------------------------------------------------------
    */

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password', [
            'submitRoute' => 'patient.password.email',
            'loginRoute' => 'auth.login',
        ]);
    })->name('patient.password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('patients')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    })->name('patient.password.email');

    Route::get('/patient/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', [
            'token' => $token,
            'submitRoute' => 'patient.password.update',
            'loginRoute' => 'auth.login',
        ]);
    })->name('patient.password.reset');

    Route::post('/patient/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ]);

        // Must be the patients broker. Using the default (users) broker looked the
        // email up in the staff table, so no patient could ever complete a reset.
        $status = Password::broker('patients')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Patient $patient, string $password) {
                $patient->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $patient->save();

                event(new PasswordReset($patient));
            }
        );

        return $status === Password::PasswordReset
            ? redirect()->route('auth.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('patient.password.update');
});

/*
|--------------------------------------------------------------------------
| Staff password reset  (guard: web, broker: users)
|--------------------------------------------------------------------------
|
| Deliberately NOT scoped to a Filament panel. Choosing a password is an
| identity operation; which panel someone may enter is an authorization
| question, enforced at login by User::canAccessPanel(). Tying the two
| together meant a role change between invitation and reset silently
| invalidated the link, reported as though the account did not exist.
|
| No guest middleware either: an already-signed-in user following an
| invitation link should see the form, not be bounced to a dashboard.
*/
Route::prefix('staff')->group(function () {
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password', [
            'submitRoute' => 'password.email',
            'loginRoute' => 'filament.admin.auth.login',
        ]);
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', [
            'token' => $token,
            'submitRoute' => 'password.update',
            'loginRoute' => 'filament.admin.auth.login',
        ]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            return back()->withErrors(['email' => [__($status)]]);
        }

        // Send them to a panel they can actually reach, rather than assuming admin.
        $user = User::where('email', $request->input('email'))->first();
        $route = ($user && $user->canAccessPanel(Filament::getPanel('admin')))
            ? 'filament.admin.auth.login'
            : 'filament.staff.auth.login';

        return redirect()->route($route)->with('status', __($status));
    })->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth:patient')
    ->name('auth.logout');

/////////////////////////////////////////////////////////////Email Verification//////////////////////////////////////////////
Route::middleware('auth:patient')->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice'); ///It is important that the route is assigned this exact name since the verified middleware included with Laravel will automatically redirect to this route name if a user has not verified their email address.

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user('patient')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    // Not using Illuminate\Foundation\Auth\EmailVerificationRequest here: it resolves
    // $request->user() against the default ("web") guard, not the "patient" guard.
    Route::get('/email/verify/{id}/{hash}', function (Request $request) {
        $patient = $request->user('patient');

        abort_unless(
            hash_equals((string) $request->route('id'), (string) $patient->getKey())
                && hash_equals((string) $request->route('hash'), sha1($patient->getEmailForVerification())),
            403
        );

        if (! $patient->hasVerifiedEmail()) {
            $patient->markEmailAsVerified();
        }

        return redirect()->route('home');
    })->middleware('signed')->name('verification.verify');


});
