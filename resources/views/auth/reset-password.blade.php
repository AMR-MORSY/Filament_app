<x-site-layout title="Set a new password">
    <div class="max-w-sm mx-auto px-4 py-16">
        <h1 class="text-xl font-semibold mb-6">Set a new password</h1>

        <form method="POST" action="{{ route('patient.password.update') }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label class="form-control">
                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Email</span>
                <input type="email" name="email" value="{{ old('email', request('email')) }}" required autofocus
                       class="input input-bordered w-full">
                @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </label>
            <label class="form-control">
                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">New password</span>
                <input type="password" name="password" required class="input input-bordered w-full">
                @error('password') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </label>
            <label class="form-control">
                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Confirm password</span>
                <input type="password" name="password_confirmation" required class="input input-bordered w-full">
            </label>
            <button type="submit" class="btn btn-primary rounded-lg">Set password</button>
        </form>
    </div>
</x-site-layout>
