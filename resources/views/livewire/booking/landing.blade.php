<div>
    @php
        $heroImages = [
            asset('images/hero/hero-1-consultation.webp'),
            asset('images/hero/hero-2-waiting-room.webp'),
            asset('images/hero/hero-3-booking-phone.webp'),
            asset('images/hero/hero-4-nurse-tablet.webp'),
            asset('images/hero/hero-5-doctor-child.webp'),
        ];
    @endphp

    {{--
        The hero leads with real availability rather than a photograph. A visitor
        arrives asking one question — how soon can I be seen — and the answer is
        already on screen, one tap from the details form.
    --}}
    <section class="relative overflow-hidden border-b border-base-300">
        <div class="max-w-6xl mx-auto px-5 sm:px-8">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-center py-14 sm:py-20">

                <div class="lg:col-span-7 max-w-xl">
                    <p class="font-mono text-[0.7rem] uppercase tracking-[0.18em] text-primary mb-5">
                        Outpatient booking
                    </p>

                    <h1 class="font-display text-[2.1rem] leading-[1.14] sm:text-5xl sm:leading-[1.1]">
                        See a doctor,<br>
                        <span class="font-light text-primary">without the phone call.</span>
                    </h1>

                    <p class="mt-6 text-base sm:text-lg leading-relaxed text-base-content/60">
                        Choose a specialty, pick a time that is genuinely free, and you are booked.
                        Three steps, no account needed.
                    </p>

                    {{-- Live openings. Every time here is a real, bookable slot. --}}
                    <div class="mt-9 rounded-box border border-base-300 bg-base-200/70">
                        <div class="flex items-baseline justify-between px-4 pt-3.5 pb-2">
                            <span class="font-mono text-[0.68rem] uppercase tracking-[0.16em] text-base-content/45">
                                Next available
                            </span>
                            @if ($openings->isNotEmpty())
                                <span class="text-xs text-base-content/40">Tap to book</span>
                            @endif
                        </div>

                        @if ($openings->isNotEmpty())
                            <ul class="divide-y divide-base-300 border-t border-base-300">
                                @foreach ($openings as $opening)
                                    <li>
                                        <a href="{{ route('booking.create', [
                                                'doctor' => $opening['doctor']->id,
                                                'date' => $opening['date']->toDateString(),
                                                'time' => $opening['time'],
                                            ]) }}" wire:navigate
                                           class="group flex items-center gap-3.5 px-4 py-3 transition-colors hover:bg-base-100">
                                            <x-slot-chip :time="$opening['time']" state="available" size="md"
                                                         class="group-hover:bg-primary group-hover:text-primary-content group-hover:border-primary" />
                                            <span class="min-w-0 flex-1">
                                                <span class="block text-sm font-medium truncate">{{ $opening['doctor']->name }}</span>
                                                <span class="block text-xs text-base-content/50 truncate">
                                                    {{ $opening['doctor']->specialty ?: $opening['doctor']->clinic->name }}
                                                </span>
                                            </span>
                                            <span class="font-mono text-xs text-base-content/45 whitespace-nowrap">
                                                {{ $opening['date']->isToday() ? 'Today' : ($opening['date']->isTomorrow() ? 'Tomorrow' : $opening['date']->format('D j M')) }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="border-t border-base-300 px-4 py-5 text-sm text-base-content/50">
                                No openings this week.
                                <a href="{{ route('search') }}" wire:navigate class="text-primary underline underline-offset-2">
                                    See the full schedule
                                </a>.
                            </div>
                        @endif
                    </div>

                    {{-- Step 1, in place. --}}
                    <form wire:submit="search"
                          class="mt-4 rounded-box border border-base-300 bg-base-100 shadow-sm flex flex-col sm:flex-row sm:items-stretch divide-y sm:divide-y-0 sm:divide-x divide-base-300 overflow-hidden">
                        <label class="flex-1 px-4 py-3 cursor-pointer">
                            <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45">
                                Specialty
                            </span>
                            <select wire:model="clinic_id"
                                    class="mt-1 w-full bg-transparent text-sm focus:outline-none cursor-pointer">
                                <option value="">Any specialty</option>
                                @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="flex-1 px-4 py-3 cursor-pointer">
                            <span class="block font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45">
                                When
                            </span>
                            <select wire:model="when"
                                    class="mt-1 w-full bg-transparent text-sm focus:outline-none cursor-pointer">
                                <option value="soonest">Soonest available</option>
                                <option value="today">Today</option>
                                <option value="3days">Next 3 days</option>
                                <option value="week">This week</option>
                            </select>
                        </label>

                        <button type="submit"
                                class="btn btn-primary rounded-none border-0 px-7 h-auto min-h-0 py-4 sm:py-0 font-medium shadow-none">
                            Find a time
                        </button>
                    </form>
                </div>

                {{-- Photography supports; it no longer competes. --}}
                <div class="lg:col-span-5 hidden lg:block">
                    <div class="hero-frame relative aspect-[4/5] overflow-hidden rounded-box bg-base-200"
                         x-data="{ current: 0, images: @js($heroImages) }"
                         x-init="setInterval(() => current = (current + 1) % images.length, 8000)"
                         aria-hidden="true">
                        <template x-for="(image, index) in images" :key="index">
                            <img :src="image" alt=""
                                 class="absolute inset-0 h-full w-full object-cover transition-opacity duration-[1600ms] ease-in-out"
                                 :class="current === index ? 'opacity-100' : 'opacity-0'">
                        </template>
                        {{-- A whisper of bone at the edges so the photograph sits in the page rather than on it. --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-base-100/35 to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Booking is an ordered process, so the numbers carry real information. --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-20"
             x-data="{ shown: false }" x-intersect.once="shown = true">
        <h2 class="font-display text-3xl sm:text-[2.5rem] leading-tight tracking-tight mb-10">
            Three steps, start to finish
        </h2>

        <ol class="grid sm:grid-cols-3 gap-y-9 gap-x-10">
            @foreach ([
                ['Tell us what you need', 'Choose a specialty and roughly when suits you. Skip either one and we will show you everything.'],
                ['Pick an open time', 'Doctors and their free slots sit on one screen. Every time you can see is a time you can have.'],
                ['Confirm', 'A name and a phone number. You will have the appointment before you have finished reading this.'],
            ] as $i => [$heading, $body])
                <li class="reveal border-t border-base-300 pt-5" :class="shown && 'is-in'"
                    style="transition-delay: {{ $i * 90 }}ms">
                    <span class="font-mono text-xs text-primary tracking-[0.16em]">0{{ $i + 1 }}</span>
                    <h3 class="font-display text-xl mt-2.5 mb-2">{{ $heading }}</h3>
                    <p class="text-sm leading-relaxed text-base-content/55">{{ $body }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Clinic tiles lead with when, not how many. --}}
    @if ($clinics->isNotEmpty())
        <section class="border-y border-base-300 bg-base-200/50">
            <div class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-20"
                 x-data="{ shown: false }" x-intersect.once="shown = true">
                <div class="flex flex-wrap items-baseline justify-between gap-3 mb-8">
                    <h2 class="font-display text-3xl sm:text-[2.5rem] leading-tight tracking-tight">
                        Where you will be seen
                    </h2>
                    <a href="{{ route('search') }}" wire:navigate
                       class="text-sm text-primary hover:underline underline-offset-4">See every doctor</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-base-300 rounded-box overflow-hidden border border-base-300">
                    @foreach ($clinics as $clinic)
                        @php $soonest = $soonestByClinic->get($clinic->id); @endphp
                        <a href="{{ route('search', ['clinic' => $clinic->id]) }}" wire:navigate
                           class="reveal group bg-base-100 p-5 sm:p-6 flex flex-col gap-3 transition-colors hover:bg-accent/45"
                           :class="shown && 'is-in'"
                           style="transition-delay: {{ min($loop->index, 5) * 60 }}ms">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-display text-xl leading-snug">{{ $clinic->name }}</h3>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"
                                     class="h-4 w-4 mt-1 shrink-0 text-base-content/25 transition-all group-hover:text-primary group-hover:translate-x-0.5">
                                    <path d="M5 12h13m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                            <p class="text-xs text-base-content/45">
                                {{ $clinic->doctors_count }} {{ Str::plural('doctor', $clinic->doctors_count) }}
                                @if ($clinic->floor)
                                    &middot; {{ $clinic->floor }}
                                @endif
                            </p>

                            <div class="mt-auto pt-2">
                                @if ($soonest)
                                    <span class="flex items-center gap-2">
                                        <x-slot-chip :time="$soonest['time']" state="available" size="sm" />
                                        <span class="font-mono text-[0.7rem] text-base-content/45">
                                            {{ $soonest['date']->isToday() ? 'today' : ($soonest['date']->isTomorrow() ? 'tomorrow' : $soonest['date']->format('D j M')) }}
                                        </span>
                                    </span>
                                @elseif ($clinic->doctors_count === 0)
                                    <span class="font-mono text-[0.7rem] text-base-content/35">No doctors listed yet</span>
                                @else
                                    <span class="font-mono text-[0.7rem] text-base-content/35">Nothing open this week</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Social proof. Numbers come from the database; quotes come from config. --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-20"
             x-data="{ shown: false }" x-intersect.once="shown = true">

        <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-5 mb-10">
            <h2 class="font-display text-3xl sm:text-[2.5rem] leading-tight">
                Why patients trust us
            </h2>

            {{-- Real counts, not claims. --}}
            <dl class="flex gap-8 sm:gap-10">
                <div>
                    <dt class="sr-only">Doctors</dt>
                    <dd class="font-display text-2xl sm:text-3xl text-primary leading-none">
                        {{ $clinics->sum('doctors_count') }}
                    </dd>
                    <p class="text-xs text-base-content/50 mt-1.5">
                        {{ Str::plural('doctor', $clinics->sum('doctors_count')) }}
                    </p>
                </div>
                <div>
                    <dt class="sr-only">Specialties</dt>
                    <dd class="font-display text-2xl sm:text-3xl text-primary leading-none">
                        {{ $clinics->count() }}
                    </dd>
                    <p class="text-xs text-base-content/50 mt-1.5">
                        {{ Str::plural('specialty', $clinics->count()) }}
                    </p>
                </div>
                <div>
                    <dt class="sr-only">Booking time</dt>
                    <dd class="font-display text-2xl sm:text-3xl text-primary leading-none">3</dd>
                    <p class="text-xs text-base-content/50 mt-1.5">steps to book</p>
                </div>
            </dl>
        </div>

        <div class="grid sm:grid-cols-3 gap-5">
            @foreach (config('clinic.testimonials', []) as $i => $testimonial)
                <figure class="reveal rounded-box border border-base-300 bg-base-200/60 p-6 flex flex-col"
                        :class="shown && 'is-in'" style="transition-delay: {{ $i * 90 }}ms">
                    @if ($rating = $testimonial['rating'] ?? null)
                        <div class="flex gap-0.5 mb-4" role="img"
                             aria-label="{{ $rating }} out of 5">
                            @for ($star = 0; $star < 5; $star++)
                                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 {{ $star < $rating ? 'text-warning' : 'text-base-300' }}"
                                     fill="currentColor" aria-hidden="true">
                                    <path d="M8 1.7l1.9 3.9 4.3.6-3.1 3 .7 4.3L8 11.5l-3.8 2 .7-4.3-3.1-3 4.3-.6z" />
                                </svg>
                            @endfor
                        </div>
                    @endif

                    <blockquote class="text-sm leading-relaxed text-base-content/75 flex-1">
                        &ldquo;{{ $testimonial['quote'] }}&rdquo;
                    </blockquote>

                    <figcaption class="flex items-center gap-3 mt-5 pt-5 border-t border-base-300">
                        <span aria-hidden="true"
                              class="h-9 w-9 shrink-0 rounded-full bg-accent text-accent-content flex items-center justify-center text-sm font-medium">
                            {{ Str::substr($testimonial['name'], 0, 1) }}
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-medium truncate">{{ $testimonial['name'] }}</span>
                            @if (! empty($testimonial['context']))
                                <span class="block text-xs text-base-content/50 truncate">{{ $testimonial['context'] }}</span>
                            @endif
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>

    {{-- Three promises the product actually keeps. --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-20">
        <div class="grid sm:grid-cols-3 gap-8 sm:gap-10">
            <div class="flex gap-4">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6 shrink-0 text-primary" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5" />
                    <path d="M3 10h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    <path d="m9 15 2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round" />
                </svg>
                <div>
                    <h3 class="font-medium mb-1.5">Every time shown is free</h3>
                    <p class="text-sm leading-relaxed text-base-content/55">
                        Slots come straight from each doctor's schedule. If you can see it, you can book it.
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6 shrink-0 text-primary" aria-hidden="true">
                    <path d="M3.2 12a8.8 8.8 0 1 0 2.9-6.5" stroke="currentColor" stroke-width="1.5"
                          stroke-linecap="round" />
                    <path d="M3 4v4h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round" />
                    <path d="M12 8v4.2l3 1.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round" />
                </svg>
                <div>
                    <h3 class="font-medium mb-1.5">Cancel free up to 24 hours before</h3>
                    <p class="text-sm leading-relaxed text-base-content/55">
                        Change your mind, or your week. Cancelling takes one tap and costs nothing.
                    </p>
                </div>
            </div>

            <div class="flex gap-4">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6 shrink-0 text-primary" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.5" />
                    <path d="M4.8 20a7.2 7.2 0 0 1 14.4 0" stroke="currentColor" stroke-width="1.5"
                          stroke-linecap="round" />
                </svg>
                <div>
                    <h3 class="font-medium mb-1.5">Book without an account</h3>
                    <p class="text-sm leading-relaxed text-base-content/55">
                        Book as a guest today. Create an account later if you want your visits in one place.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
