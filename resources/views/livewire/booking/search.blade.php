<div class="max-w-6xl mx-auto px-5 sm:px-8 py-8 sm:py-10">
    <x-step-rail :step="2" :date="$railDate" :time="$railTime" :duration="$railDuration" class="mb-9" />

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

        {{-- Filters --}}
        <aside class="lg:w-52 shrink-0 lg:sticky lg:top-24 lg:self-start flex flex-col gap-7">
            <div>
                <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-2.5">
                    Within
                </p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach (['today' => 'Today', '3days' => '3 days', 'week' => 'This week'] as $value => $label)
                        <button type="button" wire:click="$set('window', '{{ $value }}')"
                                aria-pressed="{{ $window === $value ? 'true' : 'false' }}"
                                class="rounded-field border px-2.5 py-1.5 text-xs transition-colors
                                    {{ $window === $value
                                        ? 'border-primary bg-primary text-primary-content'
                                        : 'border-base-300 text-base-content/65 hover:border-primary/50 hover:text-primary' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($clinics->isNotEmpty())
                <div>
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-2.5">
                        Specialty
                    </p>
                    <div class="flex flex-col gap-1">
                        @foreach ($clinics as $clinic)
                            <label class="flex items-center gap-2.5 text-sm cursor-pointer py-0.5 text-base-content/75 hover:text-base-content transition-colors">
                                <input type="checkbox" class="checkbox checkbox-xs rounded-[3px]"
                                       @checked($clinicId === $clinic->id)
                                       wire:click="toggleClinic({{ $clinic->id }})">
                                {{ $clinic->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-2.5">
                    Time of day
                </p>
                <div class="flex flex-col gap-1">
                    @foreach (['morning' => 'Morning', 'afternoon' => 'Afternoon'] as $value => $label)
                        <label class="flex items-center gap-2.5 text-sm cursor-pointer py-0.5 text-base-content/75 hover:text-base-content transition-colors">
                            <input type="checkbox" class="checkbox checkbox-xs rounded-[3px]"
                                   @checked(in_array($value, $timeOfDay))
                                   wire:click="toggleTimeOfDay('{{ $value }}')">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Results. Doctor and time are chosen here, on one screen. --}}
        <div class="flex-1 min-w-0 flex flex-col gap-3" wire:loading.class="opacity-60">
            <div class="flex items-baseline justify-between gap-3 mb-1">
                <h1 class="font-display text-2xl sm:text-3xl leading-tight">
                    {{ $results->count() }} {{ Str::plural('doctor', $results->count()) }} with openings
                </h1>
                @if ($results->isNotEmpty())
                    <span class="text-xs text-base-content/40 whitespace-nowrap hidden sm:inline">Soonest first</span>
                @endif
            </div>

            @forelse ($results as $row)
                @php $isOpen = $expandedDoctorId === $row->doctor->id; @endphp

                <article wire:key="doctor-{{ $row->doctor->id }}"
                         class="rounded-box border bg-base-100 transition-colors {{ $isOpen ? 'border-primary/45 shadow-sm' : 'border-base-300' }}">

                    <button type="button" wire:click="toggleDoctor({{ $row->doctor->id }})"
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                            class="w-full text-left p-4 sm:p-5 flex gap-4 items-start">
                        <x-doctor-avatar :doctor="$row->doctor" size="lg" shape="panel" />

                        <div class="flex-1 min-w-0">
                            <h2 class="font-display text-lg leading-snug">{{ $row->doctor->name }}</h2>
                            <p class="text-xs text-base-content/50 mt-0.5">
                                {{ $row->doctor->specialty ?: $row->doctor->clinic->name }}
                                @if ($row->doctor->clinic->floor)
                                    &middot; {{ $row->doctor->clinic->floor }}
                                @endif
                            </p>

                            @unless ($isOpen)
                                {{-- Collapsed preview: the soonest few times, so scanning is enough to decide. --}}
                                <div class="flex items-center gap-2 mt-3 flex-wrap">
                                    <span class="font-mono text-[0.7rem] text-base-content/45 w-16 shrink-0">
                                        {{ $row->nextDay->isToday() ? 'Today' : ($row->nextDay->isTomorrow() ? 'Tmrw' : $row->nextDay->format('D j')) }}
                                    </span>
                                    @foreach ($row->slots->take(4) as $slot)
                                        <x-slot-chip :time="$slot" size="sm" />
                                    @endforeach
                                    @if ($row->slots->count() > 4)
                                        <span class="font-mono text-[0.7rem] text-base-content/40">
                                            +{{ $row->slots->count() - 4 }}
                                        </span>
                                    @endif
                                </div>
                            @endunless
                        </div>

                        <span class="shrink-0 flex items-center gap-1.5 text-xs {{ $isOpen ? 'text-primary' : 'text-base-content/45' }}">
                            <span class="hidden sm:inline">{{ $isOpen ? 'Close' : 'See times' }}</span>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"
                                 class="h-4 w-4 transition-transform duration-200 {{ $isOpen ? 'rotate-180' : '' }}">
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                      stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>

                    @if ($isOpen)
                        {{--
                            Rendered on demand rather than mounted for every result: each
                            picker walks a week of availability, so mounting them all would
                            cost a query storm to show one.
                        --}}
                        <div class="border-t border-base-300 p-4 sm:p-5 animate-rise">
                            <livewire:booking.slot-picker :doctor="$row->doctor"
                                                          :key="'picker-'.$row->doctor->id" />

                            <p class="mt-4 text-xs text-base-content/40">
                                <a href="{{ route('doctors.show', $row->doctor) }}" wire:navigate
                                   class="hover:text-primary underline underline-offset-2 transition-colors">
                                    Open {{ $row->doctor->name }}'s full schedule
                                </a>
                            </p>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-box border border-dashed border-base-300 bg-base-200/50 px-6 py-16 text-center">
                    <p class="font-display text-xl mb-2">Nothing open in that window</p>
                    <p class="text-sm text-base-content/55 mb-5">
                        Widen the dates or clear a filter and there will usually be something within the week.
                    </p>
                    <button type="button" wire:click="$set('window', 'week')"
                            class="btn btn-primary btn-sm rounded-field px-5 shadow-none">
                        Look at the whole week
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>
