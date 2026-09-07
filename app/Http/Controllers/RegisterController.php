<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Rules\ValidPhoneNumber;
use App\Support\PhoneNumber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function register(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:40',
            'country' => ['required', Rule::in(array_keys(config('clinic.countries', [])))],
            // Validated against the chosen country, not a length regex: "999999"
            // is six digits and still not a real Egyptian number.
            'phone_number' => ['required', 'string', new ValidPhoneNumber($request->input('country'))],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:255',
            'email' => 'required|lowercase|string|email|max:255|unique:patients',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()]
        ], [
            'phone_number.regex' => 'Enter your number in digits only, without the leading zero or spaces.',
        ]);

        // libphonenumber applies the country's own trunk-prefix rule, so Egypt
        // drops its leading 0 and Italy keeps its own. Already known valid above.
        $validated['phone'] = PhoneNumber::toE164($validated['phone_number'], $validated['country']);

        unset($validated['country'], $validated['phone_number']);

        // Validated separately: the value being checked is composed above, so it
        // is not a request field and cannot go through $request->validate().
        Validator::make($validated, [
            'phone' => 'unique:patients,phone',
        ], [
            'phone.unique' => 'An account already exists with this phone number. Sign in instead.',
        ])->validateWithBag('default');

        $user = Patient::create([
            // 'first_name' => $validated['first_name'],
            // 'last_name' => $validated['last_name'],
            // 'email' => $validated['email'],
            ...$validated,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);



        event(new Registered($user));
        Auth::guard('patient')->login($user);





        return redirect()->intended(route('home'));
    }
}
