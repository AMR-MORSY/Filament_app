<x-site-layout title="Verify your email">
    <div class="max-w-sm mx-auto px-5 py-16 sm:py-20 text-center">
        <svg viewBox="0 0 24 24" fill="none" class="h-10 w-10 mx-auto text-primary mb-5" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5" />
            <path d="m3.5 7 8.5 6 8.5-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
        </svg>

        <h1 class="font-display text-3xl leading-tight mb-3">Check your inbox</h1>
        <p class="text-sm text-base-content/55 mb-7 leading-relaxed">
            We sent a verification link to
            <span class="text-base-content/80">{{ auth('patient')->user()->email }}</span>.
            Open it and My visits unlocks.
        </p>

        @if (session('status') === 'verification-link-sent')
            <p role="status" class="rounded-field border border-primary/30 bg-accent/60 px-4 py-3 text-sm mb-5">
                A new link is on its way.
            </p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary rounded-field w-full font-medium shadow-none">
                Send it again
            </button>
        </form>

        <form method="POST" action="{{ route('auth.logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm w-full font-normal text-base-content/55">Sign out</button>
        </form>

        <p class="text-xs text-base-content/40 mt-8 leading-relaxed">
            Your appointment is already booked. Verifying only unlocks managing it here.
        </p>
    </div>
</x-site-layout>
