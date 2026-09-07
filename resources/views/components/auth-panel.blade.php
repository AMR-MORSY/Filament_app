@props([
    // 'signin' | 'register' | null (no tabs — used by the password and verify screens)
    'tab' => null,
    'heading' => null,
])

<div class="max-w-3xl mx-auto px-5 sm:px-8 py-12 sm:py-16">
    <div class="rounded-box border border-base-300 bg-base-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row">

            {{-- The reason to have an account, stated once for every auth screen. --}}
            <div class="sm:w-60 shrink-0 bg-base-200/70 p-6 sm:p-7 flex flex-col border-b sm:border-b-0 sm:border-r border-base-300">
                <x-brand-mark class="h-7 w-7 text-primary mb-4" />
                <p class="font-display text-2xl leading-snug">Your visits,<br>in one place</p>
                <ul class="mt-5 flex flex-col gap-3 text-sm text-base-content/60">
                    @foreach ([
                        'Rebook a doctor you liked in one tap',
                        'Reminders before every visit',
                        'Cancel or reschedule without a phone call',
                    ] as $benefit)
                        <li class="flex gap-2.5">
                            <svg viewBox="0 0 16 16" fill="none" class="h-4 w-4 mt-0.5 shrink-0 text-primary"
                                 aria-hidden="true">
                                <path d="m3.5 8.4 3 3 6-6.5" stroke="currentColor" stroke-width="1.6"
                                      stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="leading-snug">{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-auto pt-6 text-xs text-base-content/40 leading-relaxed">
                    You never need an account to book — this only makes the next time faster.
                </p>
            </div>

            <div class="flex-1 min-w-0 p-6 sm:p-8 flex flex-col gap-5">
                @if ($tab)
                    <div class="inline-flex w-fit rounded-field border border-base-300 bg-base-200/70 p-1 gap-1">
                        <a href="{{ route('auth.login') }}" wire:navigate
                           class="rounded-[5px] px-3.5 py-1.5 text-sm transition-colors
                               {{ $tab === 'signin' ? 'bg-base-100 text-base-content shadow-sm font-medium' : 'text-base-content/55 hover:text-base-content' }}">
                            Sign in
                        </a>
                        <a href="{{ route('auth.register') }}" wire:navigate
                           class="rounded-[5px] px-3.5 py-1.5 text-sm transition-colors
                               {{ $tab === 'register' ? 'bg-base-100 text-base-content shadow-sm font-medium' : 'text-base-content/55 hover:text-base-content' }}">
                            Register
                        </a>
                    </div>
                @endif

                @if ($heading)
                    <h1 class="font-display text-2xl leading-snug">{{ $heading }}</h1>
                @endif

                @if (session('status'))
                    <p role="status" class="rounded-field border border-primary/30 bg-accent/60 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </p>
                @endif

                {{ $slot }}
            </div>
        </div>
    </div>
</div>
