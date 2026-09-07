<div class="max-w-4xl mx-auto px-5 sm:px-8 py-8 sm:py-10">
    <div class="flex items-baseline justify-between gap-4 flex-wrap mb-7">
        <h1 class="font-display text-3xl sm:text-4xl leading-tight tracking-tight">My visits</h1>
        <a href="{{ route('search') }}" wire:navigate
           class="btn btn-primary btn-sm rounded-field px-4 font-medium shadow-none">Book a visit</a>
    </div>

    <div class="flex gap-1 border-b border-base-300 mb-6 -mx-1 overflow-x-auto">
        @foreach (['upcoming' => 'Upcoming', 'past' => 'Past', 'cancelled' => 'Cancelled'] as $value => $label)
            <button type="button" wire:click="$set('tab', '{{ $value }}')"
                    aria-pressed="{{ $tab === $value ? 'true' : 'false' }}"
                    class="relative px-3 pb-3 pt-1 text-sm whitespace-nowrap transition-colors
                        {{ $tab === $value ? 'text-base-content font-medium' : 'text-base-content/50 hover:text-base-content' }}">
                {{ $label }}
                <span class="font-mono text-xs ml-1 {{ $tab === $value ? 'text-primary' : 'text-base-content/35' }}">
                    {{ $counts[$value] }}
                </span>
                @if ($tab === $value)
                    <span class="absolute inset-x-2 -bottom-px h-0.5 bg-primary rounded-full"></span>
                @endif
            </button>
        @endforeach
    </div>

    @if ($appointments->isEmpty())
        <div class="rounded-box border border-dashed border-base-300 bg-base-200/50 px-6 py-16 text-center">
            <p class="font-display text-xl mb-2">
                {{ match ($tab) {
                    'past' => 'No past visits yet',
                    'cancelled' => 'Nothing cancelled',
                    default => 'No visits booked',
                } }}
            </p>
            <p class="text-sm text-base-content/55 mb-5">
                {{ match ($tab) {
                    'past' => 'Once you have been seen, your visit history will appear here.',
                    'cancelled' => 'Anything you cancel will be kept here for your records.',
                    default => 'Find a doctor and pick a time. It takes about a minute.',
                } }}
            </p>
            @if ($tab === 'upcoming')
                <a href="{{ route('search') }}" wire:navigate
                   class="btn btn-primary btn-sm rounded-field px-5 shadow-none">Find a time</a>
            @endif
        </div>
    @else
        <div class="rounded-box border border-base-300 bg-base-100 overflow-hidden divide-y divide-base-300">
            @foreach ($appointments as $appointment)
                <div class="flex items-center gap-4 p-4 sm:p-5 flex-wrap">
                    {{-- Date block, mono so the column stays true down the list. --}}
                    <div class="w-14 shrink-0 text-center rounded-field border border-base-300 bg-base-200/60 py-2">
                        <div class="font-mono text-[0.58rem] uppercase tracking-wider text-base-content/45">
                            {{ $appointment->appointment_date->format('M') }}
                        </div>
                        <div class="font-mono text-lg leading-tight">{{ $appointment->appointment_date->format('d') }}</div>
                    </div>

                    <div class="flex-1 min-w-40">
                        <div class="flex items-center gap-2.5">
                            <x-slot-chip :time="$appointment->start_time"
                                         :state="in_array($appointment->status, ['cancelled', 'no_show']) ? 'taken' : 'available'"
                                         size="sm" />
                            <span class="font-medium truncate">{{ $appointment->doctor->name }}</span>
                        </div>
                        <p class="text-xs text-base-content/50 mt-1.5">
                            {{ $appointment->doctor->clinic->name }}
                            @if ($appointment->doctor->clinic->floor) &middot; {{ $appointment->doctor->clinic->floor }} @endif
                        </p>
                    </div>

                    <span class="rounded-field border px-2 py-1 font-mono text-[0.6rem] uppercase tracking-wider
                        {{ match ($appointment->status) {
                            'confirmed' => 'text-primary border-primary/40 bg-accent/50',
                            'pending' => 'text-warning border-warning/40',
                            'cancelled' => 'text-base-content/35 border-base-300',
                            'completed' => 'text-base-content/50 border-base-300',
                            default => 'text-base-content/50 border-base-300',
                        } }}">
                        {{ str_replace('_', ' ', $appointment->status) }}
                    </span>

                    @if ($tab === 'upcoming')
                        <div class="flex gap-2">
                            <button type="button" wire:click="reschedule({{ $appointment->id }})"
                                    wire:confirm="Reschedule this visit? The current time will be released so someone else can take it."
                                    class="btn btn-sm rounded-field border-base-300 bg-base-100 hover:border-primary hover:text-primary shadow-none font-normal">
                                Reschedule
                            </button>
                            <button type="button" wire:click="cancel({{ $appointment->id }})"
                                    wire:confirm="Cancel this visit? This cannot be undone, but you can book again at any time."
                                    class="btn btn-sm rounded-field border-base-300 bg-base-100 hover:border-error hover:text-error shadow-none font-normal">
                                Cancel
                            </button>
                        </div>
                    @elseif ($tab === 'past')
                        <a href="{{ route('doctors.show', $appointment->doctor_id) }}" wire:navigate
                           class="btn btn-sm rounded-field border-base-300 bg-base-100 hover:border-primary hover:text-primary shadow-none font-normal">
                            Book again
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
