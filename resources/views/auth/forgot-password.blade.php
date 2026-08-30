<x-site-layout title="Forgot password">
    <div class="max-w-sm mx-auto px-4 py-16">
        <h1 class="text-xl font-semibold mb-2">Reset your password</h1>
        <p class="text-sm text-base-content/60 mb-6">Enter your email and we'll send you a link to set a new password.</p>

        @if (session('status'))
            <div role="alert" class="alert alert-success text-sm mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
            @csrf
            <label class="form-control">
                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="input input-bordered w-full">
                @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </label>
            <button type="submit" class="btn btn-primary rounded-lg">Email reset link</button>
            <a href="{{ route('auth.login') }}" wire:navigate class="link text-sm text-center">Back to sign in</a>
        </form>
    </div>
</x-site-layout>
