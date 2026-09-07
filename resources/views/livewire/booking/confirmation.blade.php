<div class="bg-base-200/60 border-b border-base-300 min-h-[70vh]">
    <div class="max-w-lg mx-auto px-5 sm:px-8 py-12 sm:py-16">

        <div class="text-center mb-8 animate-rise">
            <svg viewBox="0 0 40 40" fill="none" class="h-11 w-11 mx-auto text-primary mb-5" aria-hidden="true">
                <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="1.5"
                        style="stroke-dasharray: 114; stroke-dashoffset: 114; animation: draw 0.75s ease-out 0.1s forwards;" />
                <path d="m12.5 20.5 5 5 10-10" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                      stroke-linejoin="round"
                      style="stroke-dasharray: 23; stroke-dashoffset: 23; animation: draw 0.45s ease-out 0.62s forwards;" />
            </svg>

            <h1 class="font-display text-4xl sm:text-[2.75rem] leading-tight tracking-tight">You're booked</h1>
            <p class="text-sm text-base-content/55 mt-3 leading-relaxed">
                Your slot is held. The clinic will confirm it shortly
                @if ($appointment->guest_email || $appointment->patient?->email)
                    and email
                    <span class="text-base-content/75">{{ $appointment->patient?->email ?: $appointment->guest_email }}</span>.
                @else
                    and call you on
                    <span class="font-mono text-base-content/75">{{ $appointment->guest_phone }}</span>.
                @endif
            </p>
        </div>

        {{--
            The ticket. Notches are circles of the page ground straddling each edge
            at the tear line, so no border or mask has to be faked.
        --}}
        <div class="relative bg-base-100 rounded-box shadow-sm animate-settle" style="animation-delay: 0.12s">

            <div class="px-6 sm:px-8 pt-8 pb-7 text-center">
                <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-base-content/40 mb-3">
                    {{ $appointment->appointment_date->isToday() ? 'Today' : $appointment->appointment_date->format('l') }}
                </p>
                <p class="font-display text-2xl leading-snug mb-4">
                    {{ $appointment->appointment_date->format('j F Y') }}
                </p>
                <p class="font-mono text-5xl sm:text-6xl leading-none tracking-tight text-primary tabular-nums">
                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                </p>
                <p class="font-mono text-xs text-base-content/40 mt-3">
                    until {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                </p>
            </div>

            {{-- Tear line --}}
            <div class="relative">
                <div class="absolute -left-2.5 top-1/2 -translate-y-1/2 h-5 w-5 rounded-full bg-base-200"></div>
                <div class="absolute -right-2.5 top-1/2 -translate-y-1/2 h-5 w-5 rounded-full bg-base-200"></div>
                <div class="mx-5 border-t border-dashed border-base-300"></div>
            </div>

            <div class="px-6 sm:px-8 py-6 flex items-center gap-4">
                <x-doctor-avatar :doctor="$appointment->doctor" size="md" />
                <div class="min-w-0 flex-1">
                    <p class="font-medium truncate">{{ $appointment->doctor->name }}</p>
                    <p class="text-xs text-base-content/50 truncate">
                        {{ $appointment->doctor->clinic->name }}@if ($appointment->doctor->clinic->floor) · {{ $appointment->doctor->clinic->floor }}@endif
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-mono text-[0.58rem] uppercase tracking-[0.16em] text-base-content/35">Ref</p>
                    <p class="font-mono text-sm tabular-nums">A-{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-5">
            <a href="{{ URL::signedRoute('appointments.ics', ['appointment' => $appointment->id]) }}"
               class="btn btn-sm h-auto py-2.5 rounded-field border-base-300 bg-base-100 hover:border-primary hover:text-primary shadow-none font-normal">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5" />
                    <path d="M3 10h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Add to calendar
            </a>
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($appointment->doctor->clinic->name . ' ' . ($appointment->doctor->clinic->floor ?? '')) }}"
               target="_blank" rel="noopener"
               class="btn btn-sm h-auto py-2.5 rounded-field border-base-300 bg-base-100 hover:border-primary hover:text-primary shadow-none font-normal">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.5"
                          stroke-linejoin="round" />
                    <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5" />
                </svg>
                Directions
            </a>
        </div>

        @if ($appointment->patient && ! $appointment->patient->hasVerifiedEmail())
            <div class="mt-6 rounded-box border border-dashed border-base-300 bg-base-100 p-5">
                <p class="font-medium mb-1.5">One step left on your account</p>
                <p class="text-sm text-base-content/55 leading-relaxed">
                    We emailed a link to set a password for
                    <span class="text-base-content/80">{{ $appointment->patient->email }}</span>.
                    Once you have, every visit lives in one place.
                </p>
                @if ($accountEmailSent)
                    <p class="text-sm text-primary mt-3">Sent. Check your inbox.</p>
                @else
                    <button type="button" wire:click="resendAccountEmail"
                            class="btn btn-sm rounded-field mt-4 border-base-300 bg-base-100 hover:border-primary hover:text-primary shadow-none font-normal">
                        Resend the email
                    </button>
                @endif
            </div>
        @elseif (! $appointment->patient)
            <div class="mt-6 rounded-box border border-dashed border-base-300 bg-base-100 p-5">
                <p class="font-medium mb-1.5">Keep track of this visit</p>
                <p class="text-sm text-base-content/55 leading-relaxed">
                    Create an account to reschedule or cancel without digging out this page.
                </p>
                <a href="{{ route('auth.register') }}" wire:navigate
                   class="btn btn-sm rounded-field mt-4 border-base-300 bg-base-100 hover:border-primary hover:text-primary shadow-none font-normal">
                    Create an account
                </a>
            </div>
        @else
            <a href="{{ route('appointments.index') }}" wire:navigate
               class="btn btn-primary rounded-field w-full mt-6 font-medium shadow-none">
                View my visits
            </a>
        @endif

        <p class="text-center text-xs text-base-content/40 mt-8">
            Need a different time? Cancel free up to 24 hours before and book again.
        </p>
    </div>
</div>
