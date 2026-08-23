<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function register(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:40',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'email' => 'required|lowercase|string|email|max:255|unique:patients',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()]
        ]);
        $user = Patient::create([
            // 'first_name' => $validated['first_name'],
            // 'last_name' => $validated['last_name'],
            // 'email' => $validated['email'],
            ...$validated,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);



        Auth::guard('patient')->login($user);





        return redirect()->intended(route('home'));
    }
}
