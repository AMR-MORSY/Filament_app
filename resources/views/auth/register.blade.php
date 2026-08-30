<x-site-layout title="Register">
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="card border border-base-300 bg-base-100 overflow-hidden">
            <div class="flex flex-col sm:flex-row">
                <div class="sm:w-56 shrink-0 bg-base-200 p-6 flex flex-col gap-3 border-b sm:border-b-0 sm:border-r border-base-300">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-base-content"></span>
                    <div class="text-xl font-semibold leading-snug">Your visits, in one place</div>
                    <ul class="mt-2 flex flex-col gap-2 text-sm text-base-content/60">
                        <li class="flex items-center gap-2"><span class="badge badge-outline badge-square h-4 w-4 rounded-full"></span> Rebook in one tap</li>
                        <li class="flex items-center gap-2"><span class="badge badge-outline badge-square h-4 w-4 rounded-full"></span> Reminders by email</li>
                        <li class="flex items-center gap-2"><span class="badge badge-outline badge-square h-4 w-4 rounded-full"></span> Cancel any time</li>
                    </ul>
                </div>

                <div class="flex-1 p-6 sm:p-8 flex flex-col gap-4">
                    <div class="tabs tabs-boxed bg-base-200 p-1 w-fit">
                        <a href="{{ route('auth.login') }}" wire:navigate class="tab">Sign in</a>
                        <span class="tab tab-active">Register</span>
                    </div>

                    <form method="POST" action="{{ route('auth.register.submit') }}" class="flex flex-col gap-4">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-4">
                            <label class="form-control">
                                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Full name</span>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="input input-bordered w-full">
                                @error('name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </label>
                            <label class="form-control">
                                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Phone</span>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                       class="input input-bordered w-full">
                                @error('phone') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <label class="form-control">
                            <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Email</span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="input input-bordered w-full">
                            @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <label class="form-control">
                                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Password</span>
                                <input type="password" name="password" required class="input input-bordered w-full">
                                @error('password') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </label>
                            <label class="form-control">
                                <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Confirm password</span>
                                <input type="password" name="password_confirmation" required class="input input-bordered w-full">
                            </label>
                        </div>
                        <div class="text-xs text-base-content/50">At least 8 characters, with upper &amp; lower case letters, a number, and a symbol.</div>

                        <button type="submit" class="btn btn-primary rounded-lg">Create account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
