<x-site-layout title="Set a new password">
    <div class="max-w-sm mx-auto px-5 py-16 sm:py-20">
        <x-brand-mark class="h-7 w-7 text-primary mb-5" />
        <h1 class="font-display text-3xl leading-tight mb-7">Set a new password</h1>

        <form method="POST" action="{{ route($submitRoute) }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Email <span class="text-primary">*</span></span>
                <input type="email" name="email" value="{{ old('email', request('email')) }}" required autofocus
                       autocomplete="email" class="input input-bordered w-full rounded-field bg-base-100">
                @error('email')
                    <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">New password <span class="text-primary">*</span></span>
                <input type="password" name="password" required autocomplete="new-password"
                       class="input input-bordered w-full rounded-field bg-base-100">
                @error('password')
                    <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Confirm password <span class="text-primary">*</span></span>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                       class="input input-bordered w-full rounded-field bg-base-100">
            </label>

            <p class="text-xs text-base-content/50 leading-relaxed">
                At least 8 characters, with upper and lower case letters, a number, and a symbol.
            </p>

            <button type="submit" class="btn btn-primary rounded-field font-medium shadow-none">Set password</button>
        </form>
    </div>
</x-site-layout>
