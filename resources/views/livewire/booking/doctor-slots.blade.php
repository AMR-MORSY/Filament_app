<div class="max-w-3xl mx-auto px-5 sm:px-8 py-8 sm:py-10">
    <x-step-rail :step="2" :date="$date" :time="$time" :duration="$duration" class="mb-9" />

    <div class="flex items-center gap-4 mb-7">
        <x-doctor-avatar :doctor="$doctor" size="lg" shape="panel" />
        <div class="min-w-0">
            <h1 class="font-display text-2xl sm:text-3xl leading-tight">{{ $doctor->name }}</h1>
            <p class="text-sm text-base-content/50 mt-1">
                {{ $doctor->specialty ?: $doctor->clinic->name }}
                @if ($doctor->clinic->floor)
                    &middot; {{ $doctor->clinic->floor }}
                @endif
            </p>
        </div>
    </div>

    @if ($doctor->bio)
        <p class="text-sm leading-relaxed text-base-content/60 border-l-2 border-base-300 pl-4 mb-7">
            {{ $doctor->bio }}
        </p>
    @endif

    <div class="rounded-box border border-base-300 bg-base-100 p-4 sm:p-6">
        <livewire:booking.slot-picker :doctor="$doctor" :date="$date" :time="$time" :standalone="true"
                                      :key="'picker-'.$doctor->id" />
    </div>

    <p class="mt-5 text-center text-xs text-base-content/40">
        Cancel free up to 24 hours before your visit.
    </p>
</div>
