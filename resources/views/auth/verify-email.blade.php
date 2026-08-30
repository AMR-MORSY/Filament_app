<x-site-layout title="Verify your email">
    <div class="max-w-sm mx-auto px-4 py-16 text-center">
        <div class="mx-auto h-12 w-12 rounded-full border-2 border-primary text-primary flex items-center justify-center text-xl mb-4">&#9993;</div>
        <h1 class="text-xl font-semibold mb-2">Verify your email</h1>
        <p class="text-sm text-base-content/60 mb-6">
            We sent a verification link to <span class="font-medium">{{ auth('patient')->user()->email }}</span>.
            Click it to unlock My Visits.
        </p>

        @if (session('status') === 'verification-link-sent')
            <div role="alert" class="alert alert-success text-sm mb-4">A new verification link has been sent.</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary rounded-lg w-full">Resend verification email</button>
        </form>

        <form method="POST" action="{{ route('auth.logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm w-full">Sign out</button>
        </form>
    </div>
</x-site-layout>
