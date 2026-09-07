<x-site-layout title="Sign in">
    <x-auth-panel tab="signin">
        <form method="POST" action="{{ route('auth.login.submit') }}" class="flex flex-col gap-4">
            @csrf

            @if ($errors->any())
                <p role="alert" class="rounded-field border border-error/35 bg-error/8 px-4 py-3 text-sm text-error">
                    {{ $errors->first() }}
                </p>
            @endif

            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Email <span class="text-primary">*</span></span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="input input-bordered w-full rounded-field bg-base-100">
            </label>

            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Password <span class="text-primary">*</span></span>
                <input type="password" name="password" required autocomplete="current-password"
                       class="input input-bordered w-full rounded-field bg-base-100">
            </label>

            <div class="flex items-center justify-between gap-3 text-sm">
                <label class="flex items-center gap-2 cursor-pointer text-base-content/70">
                    <input type="checkbox" name="remember" class="checkbox checkbox-xs rounded-[3px]">
                    Stay signed in
                </label>
                <a href="{{ route('patient.password.request') }}" wire:navigate
                   class="text-primary hover:underline underline-offset-4">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary rounded-field font-medium shadow-none mt-1">Sign in</button>
        </form>
    </x-auth-panel>
</x-site-layout>
