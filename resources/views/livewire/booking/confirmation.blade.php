<div class="max-w-xl mx-auto px-4 sm:px-8 py-12">
    <div class="text-center mb-8">
        <div class="mx-auto h-12 w-12 rounded-full border-2 border-primary text-primary flex items-center justify-center text-xl mb-3">&#10003;</div>
        <h1 class="text-2xl font-semibold">Request received</h1>
        <div class="text-xs text-base-content/50 mt-1">
            REF #A-{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }} &middot; awaiting clinic confirmation
        </div>
    </div>

    <div class="card border border-base-300 bg-base-100 mb-4">
        <div class="card-body p-4 sm:p-5 flex-row gap-4">
            <div class="avatar placeholder shrink-0">
                <div class="bg-base-300 text-base-content/40 rounded-full w-13">
                    <span class="text-lg">{{ strtoupper(substr(preg_replace('/^dr\.?\s*/i', '', $appointment->doctor->name), 0, 1)) }}</span>
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <div class="font-medium text-lg">
                    {{ $appointment->appointment_date->format('D j M') }} &middot; {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}
                </div>
                <div class="text-sm text-base-content/60">{{ $appointment->doctor->name }} &mdash; {{ $appointment->doctor->clinic->name }}</div>
                @if ($appointment->doctor->clinic->floor)
                    <div class="text-xs text-base-content/50">{{ $appointment->doctor->clinic->floor }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-6">
        <a href="{{ URL::signedRoute('appointments.ics', ['appointment' => $appointment->id]) }}"
           class="btn btn-outline btn-sm rounded-lg">Add to calendar</a>
        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($appointment->doctor->clinic->name . ' ' . ($appointment->doctor->clinic->floor ?? '')) }}"
           target="_blank" rel="noopener" class="btn btn-outline btn-sm rounded-lg">Directions</a>
    </div>

    @if ($appointment->patient && ! $appointment->patient->hasVerifiedEmail())
        <div class="card border border-dashed border-base-300 bg-base-100">
            <div class="card-body gap-2">
                <div class="font-medium">Finish your account &mdash; one step left</div>
                <div class="text-sm text-base-content/60">
                    We emailed a link to set a password for {{ $appointment->patient->email }}. Once verified you'll see all your visits in one place.
                </div>
                @if ($accountEmailSent)
                    <div class="text-sm text-success">Email sent &mdash; check your inbox.</div>
                @else
                    <button type="button" wire:click="resendAccountEmail" class="btn btn-sm btn-neutral w-fit rounded-lg">Resend email</button>
                @endif
            </div>
        </div>
    @elseif (! $appointment->patient)
        <div class="card border border-dashed border-base-300 bg-base-100">
            <div class="card-body gap-2">
                <div class="font-medium">Keep track of this visit</div>
                <div class="text-sm text-base-content/60">Create an account to manage, reschedule, or cancel your visits in one place.</div>
                <a href="{{ route('auth.register') }}" wire:navigate class="btn btn-sm btn-neutral w-fit rounded-lg">Create an account</a>
            </div>
        </div>
    @else
        <a href="{{ route('appointments.index') }}" wire:navigate class="btn btn-primary w-full rounded-lg">View my visits</a>
    @endif
</div>
