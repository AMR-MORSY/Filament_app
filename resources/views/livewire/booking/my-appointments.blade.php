<div class="max-w-4xl mx-auto px-4 sm:px-8 py-8">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div class="flex gap-2">
            <button type="button" wire:click="$set('tab', 'upcoming')"
                    class="btn btn-sm rounded-full {{ $tab === 'upcoming' ? 'btn-primary' : 'btn-outline' }}">Upcoming {{ $counts['upcoming'] }}</button>
            <button type="button" wire:click="$set('tab', 'past')"
                    class="btn btn-sm rounded-full {{ $tab === 'past' ? 'btn-primary' : 'btn-outline' }}">Past {{ $counts['past'] }}</button>
            <button type="button" wire:click="$set('tab', 'cancelled')"
                    class="btn btn-sm rounded-full {{ $tab === 'cancelled' ? 'btn-primary' : 'btn-outline' }}">Cancelled {{ $counts['cancelled'] }}</button>
        </div>
        <a href="{{ route('search') }}" wire:navigate class="btn btn-outline btn-sm rounded-lg">+ Book a visit</a>
    </div>

    @if ($appointments->isEmpty())
        <div class="text-center py-16 text-base-content/50">Nothing here yet.</div>
    @else
        <div class="card border border-base-300 bg-base-100 overflow-hidden">
            <div class="divide-y divide-base-300">
                @foreach ($appointments as $appointment)
                    <div class="flex items-center gap-4 p-4 flex-wrap">
                        <div class="w-14 shrink-0 text-center border border-base-300 rounded-lg py-1.5">
                            <div class="text-[10px] uppercase text-base-content/50">{{ $appointment->appointment_date->format('M') }}</div>
                            <div class="text-lg font-medium">{{ $appointment->appointment_date->format('d') }}</div>
                        </div>
                        <div class="flex-1 min-w-40">
                            <div class="font-medium">{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} &middot; {{ $appointment->doctor->name }}</div>
                            <div class="text-xs text-base-content/50">
                                {{ $appointment->doctor->clinic->name }}
                                @if ($appointment->doctor->clinic->floor) &middot; {{ $appointment->doctor->clinic->floor }} @endif
                            </div>
                        </div>
                        <div class="badge {{ match($appointment->status) {
                                'confirmed' => 'badge-outline text-primary border-primary',
                                'pending' => 'badge-outline text-warning border-warning',
                                'cancelled' => 'badge-outline text-base-content/40 border-base-300',
                                'completed' => 'badge-outline text-base-content/50 border-base-300',
                                default => 'badge-outline',
                            } }} uppercase text-[10px]">{{ str_replace('_', ' ', $appointment->status) }}</div>

                        @if ($tab === 'upcoming')
                            <div class="flex gap-2 text-sm">
                                <button type="button" wire:click="reschedule({{ $appointment->id }})"
                                        wire:confirm="Reschedule this visit? Your current booking will be cancelled."
                                        class="btn btn-sm btn-outline rounded-lg">Reschedule</button>
                                <button type="button" wire:click="cancel({{ $appointment->id }})"
                                        wire:confirm="Cancel this visit?"
                                        class="btn btn-sm btn-outline rounded-lg">Cancel</button>
                            </div>
                        @elseif ($tab === 'past')
                            <a href="{{ route('doctors.show', $appointment->doctor_id) }}" wire:navigate
                               class="btn btn-sm btn-outline rounded-lg">Book follow-up</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
