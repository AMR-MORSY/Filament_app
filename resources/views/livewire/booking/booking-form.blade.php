<div class="max-w-5xl mx-auto px-5 sm:px-8 py-8 sm:py-10">
    <x-step-rail :step="3" :date="$date" :time="$time" :duration="$slotDuration" class="mb-9" />

    @if ($slotTaken)
        <div role="alert"
             class="mb-7 rounded-box border border-error/35 bg-error/8 px-5 py-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-medium text-error">Someone booked that time a moment ago</p>
                <p class="text-sm text-base-content/60 mt-0.5">Nothing has been charged or saved. Pick another time.</p>
            </div>
            <a href="{{ route('doctors.show', $doctor) }}" wire:navigate
               class="btn btn-sm rounded-field border-error/40 bg-transparent text-error hover:bg-error hover:text-error-content shadow-none">
                Choose another time
            </a>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

        {{-- Details --}}
        <div class="flex-1 min-w-0 flex flex-col gap-6 {{ $slotTaken ? 'opacity-40 pointer-events-none' : '' }}">
            <div>
                <h1 class="font-display text-2xl sm:text-3xl leading-tight">Who is this visit for?</h1>
                <p class="text-sm text-base-content/55 mt-1.5">
                    A name and a number is all the clinic needs to hold your slot.
                </p>
            </div>

            @unless (auth('patient')->check())
                <div class="inline-flex w-fit rounded-field border border-base-300 bg-base-200/70 p-1 gap-1">
                    @foreach (['guest' => 'Book as guest', 'signin' => 'Sign in'] as $value => $label)
                        <button type="button" wire:click="$set('mode', '{{ $value }}')"
                                aria-pressed="{{ $mode === $value ? 'true' : 'false' }}"
                                class="rounded-[5px] px-3.5 py-1.5 text-sm transition-colors
                                    {{ $mode === $value
                                        ? 'bg-base-100 text-base-content shadow-sm font-medium'
                                        : 'text-base-content/55 hover:text-base-content' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            @endunless

            @if ($mode === 'signin' && ! auth('patient')->check())
                <div class="rounded-box border border-base-300 bg-base-100 p-5 max-w-sm flex flex-col gap-4">
                    <label class="block">
                        <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Email</span>
                        <input type="email" wire:model="signin_email" autocomplete="email"
                               class="input input-bordered w-full rounded-field bg-base-100">
                        @error('signin_email')
                            <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="block">
                        <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Password</span>
                        <input type="password" wire:model="signin_password" autocomplete="current-password"
                               wire:keydown.enter="signIn"
                               class="input input-bordered w-full rounded-field bg-base-100">
                    </label>
                    <button type="button" wire:click="signIn"
                            class="btn btn-primary rounded-field shadow-none font-medium">Sign in</button>
                </div>
            @else
                @if (auth('patient')->check())
                    <p class="rounded-field bg-accent/60 px-4 py-3 text-sm">
                        Booking as <span class="font-medium">{{ $name }}</span>
                        <span class="text-base-content/50">· {{ $email }}</span>
                    </p>
                @endif

                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="block">
                        <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                            Full name <span class="text-primary">*</span>
                        </span>
                        <input type="text" wire:model="name" autocomplete="name"
                               class="input input-bordered w-full rounded-field bg-base-100"
                               @disabled(auth('patient')->check())>
                        @error('name')
                            <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                            Phone <span class="text-primary">*</span>
                        </span>
                        <input type="tel" wire:model="phone" autocomplete="tel"
                               class="input input-bordered w-full rounded-field bg-base-100 font-mono"
                               @disabled(auth('patient')->check())>
                        @error('phone')
                            <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Email</span>
                        <input type="email" wire:model="email" autocomplete="email"
                               class="input input-bordered w-full rounded-field bg-base-100"
                               @disabled(auth('patient')->check())>
                        @error('email')
                            <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">Date of birth</span>
                        <input type="date" wire:model="date_of_birth"
                               class="input input-bordered w-full rounded-field bg-base-100 font-mono">
                        @error('date_of_birth')
                            <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <label class="block">
                    <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-1.5">
                        Reason for the visit
                    </span>
                    <textarea wire:model="notes" rows="3" maxlength="1000"
                              placeholder="Anything the doctor should know before you arrive. Optional."
                              class="textarea textarea-bordered w-full rounded-field bg-base-100 leading-relaxed"></textarea>
                    @error('notes')
                        <span class="block text-error text-xs mt-1.5">{{ $message }}</span>
                    @enderror
                </label>

                @unless (auth('patient')->check())
                    <label class="flex items-start gap-3 cursor-pointer rounded-field border border-base-300 bg-base-200/50 px-4 py-3.5">
                        <input type="checkbox" wire:model="createAccount"
                               class="checkbox checkbox-sm rounded-[3px] mt-0.5">
                        <span class="text-sm leading-relaxed">
                            Keep my visits in one place
                            <span class="block text-xs text-base-content/50 mt-0.5">
                                We will email you a link to set a password. You do not need this to book.
                            </span>
                        </span>
                    </label>
                @endunless
            @endif
        </div>

        {{-- Summary. The slot chip, full size, so the thing being agreed to is unmistakable. --}}
        <div class="lg:w-80 shrink-0">
            <div class="lg:sticky lg:top-24 rounded-box border border-base-300 bg-base-100 overflow-hidden">
                <div class="px-5 pt-5 pb-4 bg-base-200/60 border-b border-base-300">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-2.5">
                        Your appointment
                    </p>
                    <p class="font-display text-2xl leading-snug">
                        {{ \Carbon\Carbon::parse($date)->format('l j F') }}
                    </p>
                    <div class="mt-3">
                        <x-slot-chip :time="$time" :duration="$slotDuration" state="chosen" size="lg" />
                    </div>
                </div>

                <div class="p-5 flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <x-doctor-avatar :doctor="$doctor" size="md" />
                        <div class="min-w-0">
                            <p class="text-sm font-medium truncate">{{ $doctor->name }}</p>
                            <p class="text-xs text-base-content/50 truncate">
                                {{ $doctor->clinic->name }}@if ($doctor->clinic->floor) · {{ $doctor->clinic->floor }}@endif
                            </p>
                        </div>
                    </div>

                    <p class="text-xs text-base-content/50 leading-relaxed border-t border-base-300 pt-4">
                        Cancel free up to 24 hours before. The clinic will email you once it confirms.
                    </p>

                    <button type="button" wire:click="confirmBooking" @disabled($slotTaken)
                            class="btn btn-primary rounded-field w-full font-medium shadow-none disabled:opacity-40">
                        <span wire:loading.remove wire:target="confirmBooking">Book this time</span>
                        <span wire:loading wire:target="confirmBooking">Booking…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
