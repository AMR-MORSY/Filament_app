<x-site-layout title="Sign in">
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
                        <span class="tab tab-active">Sign in</span>
                        <a href="{{ route('auth.register') }}" wire:navigate class="tab">Register</a>
                    </div>

                    @if (session('status'))
                        <div role="alert" class="alert alert-success text-sm">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('auth.login.submit') }}" class="flex flex-col gap-4">
                        @csrf
                        <label class="form-control">
                            <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Email</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="input input-bordered w-full">
                        </label>
                        <label class="form-control">
                            <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Password</span>
                            <input type="password" name="password" required class="input input-bordered w-full">
                        </label>

                        @if ($errors->any())
                            <div role="alert" class="alert alert-error text-sm">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="checkbox checkbox-sm">
                                Remember me
                            </label>
                            <a href="{{ route('password.request') }}" wire:navigate class="link">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-lg">Sign in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
