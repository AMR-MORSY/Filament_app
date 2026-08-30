<div class="max-w-6xl mx-auto px-4 sm:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-56 shrink-0 flex flex-col gap-6">
            <div>
                <div class="text-xs font-medium tracking-wide text-base-content/50 uppercase mb-2">Availability</div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="$set('window', 'today')"
                            class="btn btn-sm rounded-full {{ $window === 'today' ? 'btn-primary' : 'btn-outline' }}">Today</button>
                    <button type="button" wire:click="$set('window', '3days')"
                            class="btn btn-sm rounded-full {{ $window === '3days' ? 'btn-primary' : 'btn-outline' }}">3 days</button>
                    <button type="button" wire:click="$set('window', 'week')"
                            class="btn btn-sm rounded-full {{ $window === 'week' ? 'btn-primary' : 'btn-outline' }}">Week</button>
                </div>
            </div>

            <div>
                <div class="text-xs font-medium tracking-wide text-base-content/50 uppercase mb-2">Clinic</div>
                <div class="flex flex-col gap-2">
                    @foreach ($clinics as $clinic)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" class="checkbox checkbox-sm"
                                   {{ $clinicId === $clinic->id ? 'checked' : '' }}
                                   wire:click="toggleClinic({{ $clinic->id }})">
                            {{ $clinic->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="text-xs font-medium tracking-wide text-base-content/50 uppercase mb-2">Time of day</div>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" class="checkbox checkbox-sm"
                               {{ in_array('morning', $timeOfDay) ? 'checked' : '' }}
                               wire:click="toggleTimeOfDay('morning')">
                        Morning
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" class="checkbox checkbox-sm"
                               {{ in_array('afternoon', $timeOfDay) ? 'checked' : '' }}
                               wire:click="toggleTimeOfDay('afternoon')">
                        Afternoon
                    </label>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col gap-4" wire:loading.class="opacity-60">
            <div class="flex items-baseline justify-between">
                <h1 class="text-lg font-semibold">{{ $results->count() }} {{ Str::plural('doctor', $results->count()) }} available</h1>
            </div>

            @forelse ($results as $row)
                <a href="{{ route('doctors.show', $row->doctor) }}" wire:navigate
                   class="card border border-base-300 bg-base-100 hover:border-primary transition-colors">
                    <div class="card-body p-4 sm:p-5 flex-row gap-4">
                        <div class="avatar placeholder shrink-0">
                            <div class="bg-base-300 text-base-content/40 rounded-xl w-16">
                                @if ($row->doctor->hasMedia('main_image') && $row->doctor->getFirstMediaUrl('main_image'))
                                    <img src="{{ $row->doctor->getFirstMediaUrl('main_image','thumb') }}" alt="{{ $row->doctor->name }}">
                                @else
                                    {{-- <span class="text-lg">{{ strtoupper(substr(preg_replace('/^dr\.?\s*/i', '', $row->doctor->name), 0, 1)) }}</span> --}}
                                    <img src="/images/doctors/doctor-placeholder.jpg" alt="{{ $row->doctor->name }}">
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col gap-1.5 min-w-0">
                            <div class="flex items-baseline gap-2 flex-wrap">
                                <span class="font-semibold">{{ $row->doctor->name }}</span>
                            </div>
                            <div class="text-xs text-base-content/50">
                                {{ $row->doctor->specialty }} &middot; {{ $row->doctor->clinic->name }}
                                @if ($row->doctor->clinic->floor) , {{ $row->doctor->clinic->floor }} @endif
                            </div>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="text-xs text-base-content/50 w-16 shrink-0">
                                    {{ $row->nextDay->isToday() ? 'Today' : $row->nextDay->format('D j') }}
                                </span>
                                @foreach ($row->slots->take(3) as $slot)
                                    <span class="badge badge-outline">{{ \Carbon\Carbon::parse($slot)->format('H:i') }}</span>
                                @endforeach
                                @if ($row->slots->count() > 3)
                                    <span class="badge badge-outline badge-dash">+{{ $row->slots->count() - 3 }} more</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-16 text-base-content/50">
                    No doctors available in this window. Try widening the availability filter.
                </div>
            @endforelse
        </div>
    </div>
</div>
