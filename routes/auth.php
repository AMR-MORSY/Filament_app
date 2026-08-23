<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Models\Patient;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

    ////////////////////Password-Reset-Routes///////////////////////////////////////////////////////////////////
    
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('patients')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/patient/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('patient.password.reset');


    Route::post('/patient/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()]
    ]);
 
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (Patient $patient, string $password) {
            $patient->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
 
            $patient->save();
 
            event(new PasswordReset($patient));
        }
    );
 
    return $status === Password::PasswordReset
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->name('patient.password.update');
});
