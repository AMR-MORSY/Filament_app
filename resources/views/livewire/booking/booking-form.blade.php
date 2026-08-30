<div class="max-w-4xl mx-auto px-4 sm:px-8 py-8">
    <div class="text-sm text-base-content/50 mb-6">Step 3 of 3 &mdash; your details</div>

    @if ($slotTaken)
        <div role="alert" class="alert alert-error mb-6">
            <span>That slot was just taken by someone else. Please pick another time.</span>
            <a href="{{ route('doctors.show', $doctor) }}" wire:navigate class="btn btn-sm">Choose another time</a>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1 flex flex-col gap-5 {{ $slotTaken ? 'opacity-40 pointer-events-none' : '' }}">
            @unless (auth('patient')->check())
                <div class="tabs tabs-boxed bg-base-100 border border-base-300 p-1 w-fit">
                    <button type="button" wire:click="$set('mode', 'guest')"
                            class="tab {{ $mode === 'guest' ? 'tab-active' : '' }}">Book as guest</button>
                    <button type="button" wire:click="$set('mode', 'signin')"
                            class="tab {{ $mode === 'signin' ? 'tab-active' : '' }}">Sign in</button>
                </div>
            @endunless

            @if ($mode === 'signin' && ! auth('patient')->check())
                <div class="card border border-base-300 bg-base-100 max-w-sm">
                    <div class="card-body gap-3">
                        <label class="form-control">
                            <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Email</span>
                            <input type="email" wire:model="signin_email" class="input input-bordered w-full">
                            @error('signin_email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </label>
                        <label class="form-control">
                            <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Password</span>
                            <input type="password" wire:model="signin_password" class="input input-bordered w-full">
                        </label>
                        <button type="button" wire:click="signIn" class="btn btn-primary mt-2">Sign in</button>
                    </div>
                </div>
            @else
                @if (auth('patient')->check())
                    <div class="text-sm text-base-content/60">Booking as <span class="font-medium text-base-content">{{ $name }}</span> ({{ $email }})</div>
                @endif

                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="form-control">
                        <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Full name *</span>
                        <input type="text" wire:model="name" class="input input-bordered w-full" @if (auth('patient')->check()) disabled @endif>
                        @error('name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </label>
                    <label class="form-control">
                        <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Phone *</span>
                        <input type="text" wire:model="phone" class="input input-bordered w-full" @if (auth('patient')->check()) disabled @endif>
                        @error('phone') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </label>
                    <label class="form-control">
                        <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Email</span>
                        <input type="email" wire:model="email" class="input input-bordered w-full" @if (auth('patient')->check()) disabled @endif>
                        @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </label>
                    <label class="form-control">
                        <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Date of birth</span>
                        <input type="date" wire:model="date_of_birth" class="input input-bordered w-full">
                    </label>
                </div>

                <label class="form-control">
                    <span class="label-text text-xs uppercase tracking-wide text-base-content/50">Reason for visit / notes</span>
                    <textarea wire:model="notes" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </label>

                @unless (auth('patient')->check())
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="createAccount" class="checkbox checkbox-sm mt-0.5">
                        <span class="text-sm">Create an account to manage this visit &mdash; we'll email you a link to set a password.</span>
                    </label>
                @endunless

                <div class="alert bg-base-100 border border-dashed border-base-300 text-xs text-base-content/60">
                    You'll get an instant email once the clinic confirms your appointment.
                </div>
            @endif
        </div>

        <div class="lg:w-72 shrink-0">
            <div class="card border border-base-300 bg-base-100 sticky top-6">
                <div class="card-body gap-3">
                    <div class="text-xs uppercase tracking-wide text-base-content/50">Your appointment</div>
                    <div class="text-lg font-medium leading-snug">
                        {{ \Carbon\Carbon::parse($date)->format('D j M') }}<br>
                        {{ \Carbon\Carbon::parse($time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($time)->addMinutes($slotDuration)->format('H:i') }}
                    </div>
                    <div class="divider my-0"></div>
                    <div class="flex items-center gap-3">
                        <div class="avatar placeholder shrink-0">
                            <div class="bg-base-300 text-base-content/40 rounded-full w-9">
                                <span class="text-sm">{{ strtoupper(substr(preg_replace('/^dr\.?\s*/i', '', $doctor->name), 0, 1)) }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium">{{ $doctor->name }}</div>
                            <div class="text-xs text-base-content/50">{{ $doctor->clinic->name }}</div>
                        </div>
                    </div>
                    @if ($doctor->clinic->floor)
                        <div class="text-xs text-base-content/50">{{ $doctor->clinic->floor }}</div>
                    @endif
                    <div class="divider my-0"></div>
                    <div class="text-xs text-base-content/50">Free cancellation up to 24h before</div>
                    <button type="button" wire:click="confirmBooking" @if ($slotTaken) disabled @endif
                            class="btn btn-primary rounded-lg mt-2">Confirm booking</button>
                </div>
            </div>
        </div>
    </div>
</div>
