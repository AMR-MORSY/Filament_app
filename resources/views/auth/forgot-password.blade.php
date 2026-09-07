<x-site-layout title="Forgot password">
    <div class="max-w-sm mx-auto px-5 py-16 sm:py-20">
        <x-brand-mark class="h-7 w-7 text-primary mb-5" />
        <h1 class="font-display text-3xl leading-tight mb-2">Reset your password</h1>
        <p class="text-sm text-base-content/55 mb-7 leading-relaxed">
            Enter your email and we will send a link to set a new one.
        </p>

        @if (session('status'))
            <p role="status" class="rounded-field border border-primary/30 bg-accent/60 px-4 py-3 text-sm mb-5">
                {{ session('status') }}
            </p>
        @endif

        <form method="POST" action="{{ route($submitRoute) }}" class="flex flex-col gap-4">
            @csrf
            <label class="block">
                <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Email <span class="text-primary">*</span></span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="input input-bordered w-full rounded-field bg-base-100">
                @error('email')
                    <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                @enderror
            </label>

            <button type="submit" class="btn btn-primary rounded-field font-medium shadow-none">Email the link</button>

            <a href="{{ route($loginRoute) }}" wire:navigate
               class="text-sm text-center text-base-content/55 hover:text-primary transition-colors">
                Back to sign in
            </a>
        </form>
    </div>
</x-site-layout>
