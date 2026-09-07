<x-site-layout title="Register">
    <x-auth-panel tab="register">
        <form method="POST" action="{{ route('auth.register.submit') }}" class="flex flex-col gap-4">
            @csrf

            <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                    <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                        Full name <span class="text-primary">*</span>
                    </span>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                        class="input input-bordered w-full rounded-field bg-base-100">
                    @error('name')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </label>

                {{-- Dial code and national number are collected separately, then
                     stored as one E.164-style string so the column stays searchable. --}}
                <div class="block">
                    <span
                        class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                        Phone <span class="text-primary">*</span>
                    </span>
                    <div class="flex telephone-container bg-base-100  focus-within:border-primary transition-colors">
                        <select name="country" required aria-label="Country this number belongs to"
                            class=" bg-base-200/70 select max-w-fit  px-2 py-2.5 rounded-r-none border-r-0 text-sm focus:outline-none cursor-pointer">
                            @foreach (config('clinic.countries', []) as $iso => $label)
                                <option value="{{ $iso }}" @selected(old('country', config('clinic.default_country')) === $iso)>
                                    {{ \App\Support\PhoneNumber::dialCode($iso) }} · {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required
                            inputmode="numeric" autocomplete="tel-national" placeholder="1099988877"
                            class="input border-l-0 rounded-l-none  w-full max-w-fit bg-transparent px-3 py-2.5 focus:outline-none">
                    </div>
                    @error('country')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                    @error('phone_number')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                    @error('phone')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </div>



            </div>


            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                    Email <span class="text-primary">*</span>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    class="input input-bordered w-full rounded-field bg-base-100">
                @error('email')
                    <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </label>

            {{-- Optional, and labelled as such: the product promise is a fast booking,
                 so nothing here should read as another hurdle before you can book. --}}
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                    <span
                        class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                        Date of birth <span class="normal-case tracking-normal text-base-content/35">· optional</span>
                    </span>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" autocomplete="bday"
                        max="{{ now()->toDateString() }}"
                        class="input input-bordered w-full rounded-field bg-base-100">
                    @error('date_of_birth')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span
                        class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                        Gender <span class="normal-case tracking-normal text-base-content/35">· optional</span>
                    </span>
                    <select name="gender" class="select select-bordered w-full rounded-field bg-base-100">
                        <option value="">Prefer not to say</option>
                        @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('gender')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                    Address <span class="normal-case tracking-normal text-base-content/35">· optional</span>
                </span>
                <input type="text" name="address" value="{{ old('address') }}" maxlength="255"
                    autocomplete="street-address" placeholder="Helps the clinic reach you if plans change"
                    class="input input-bordered w-full rounded-field bg-base-100">
                @error('address')
                    <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </label>

            <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                    <span
                        class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                        Password <span class="text-primary">*</span>
                    </span>
                    <input type="password" name="password" required autocomplete="new-password"
                        class="input input-bordered w-full rounded-field bg-base-100">
                    @error('password')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span
                        class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                        Confirm password <span class="text-primary">*</span>
                    </span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                        class="input   w-full rounded-field bg-base-100">
                </label>
            </div>

            <p class="text-xs text-base-content/50 leading-relaxed">
                At least 8 characters, with upper and lower case letters, a number, and a symbol.
            </p>

            <button type="submit" class="btn btn-primary rounded-field font-medium shadow-none mt-1">Create
                account</button>
        </form>
    </x-auth-panel>
</x-site-layout>
