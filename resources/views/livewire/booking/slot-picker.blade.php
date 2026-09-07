<div class="flex flex-col gap-5">

    {{-- Week strip. Day numbers are mono so the row stays on a true grid. --}}
    <div class="flex items-stretch gap-2">
        <button type="button" wire:click="prevWeek" @disabled($weekOffset === 0)
                aria-label="Previous week"
                class="w-7 sm:w-8 shrink-0 rounded-field border border-base-300 text-base-content/50 transition-colors hover:border-primary hover:text-primary disabled:opacity-30 disabled:hover:border-base-300 disabled:hover:text-base-content/50">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 mx-auto" aria-hidden="true">
                <path d="m14 6-6 6 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round" />
            </svg>
        </button>

        <div class="flex-1 min-w-0 overflow-x-auto"
             wire:loading.class="opacity-50" wire:target="prevWeek,nextWeek">
            <div class="grid grid-cols-7 gap-1.5 min-w-[266px]">
            @foreach ($days as $day)
                @php
                    $isSelected = $date === $day->iso;
                    $isClosed = $day->count === 0;
                @endphp
                <button type="button" wire:click="selectDate('{{ $day->iso }}')" @disabled($isClosed)
                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                        class="flex flex-col items-center gap-0.5 rounded-field border px-1 py-2 transition-colors
                            {{ $isSelected ? 'border-primary bg-primary text-primary-content' : 'border-base-300' }}
                            {{ ! $isSelected && ! $isClosed ? 'hover:border-primary/50 hover:bg-accent/60' : '' }}
                            {{ $isClosed ? 'opacity-35 cursor-not-allowed' : '' }}">
                    <span class="text-[0.6rem] uppercase tracking-wider {{ $isSelected ? 'opacity-75' : 'text-base-content/45' }}">
                        {{ $day->date->format('D') }}
                    </span>
                    <span class="font-mono text-base leading-none">{{ $day->date->format('j') }}</span>
                    <span class="font-mono text-[0.58rem] {{ $isSelected ? 'opacity-75' : 'text-base-content/40' }}">
                        {{ $isClosed ? '—' : $day->count }}
                    </span>
                </button>
            @endforeach
            </div>
        </div>

        <button type="button" wire:click="nextWeek" aria-label="Next week"
                class="w-7 sm:w-8 shrink-0 rounded-field border border-base-300 text-base-content/50 transition-colors hover:border-primary hover:text-primary">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 mx-auto" aria-hidden="true">
                <path d="m10 6 6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    {{-- Slots. --}}
    <div class="flex flex-col gap-4" wire:loading.class="opacity-50"
         wire:target="selectDate,prevWeek,nextWeek">
        @if ($morning->isEmpty() && $afternoon->isEmpty())
            <p class="rounded-box border border-dashed border-base-300 bg-base-200/50 py-7 text-center text-sm text-base-content/50">
                Nothing open on {{ \Carbon\Carbon::parse($date)->format('D j M') }}. Try another day.
            </p>
        @endif

        @foreach (['Morning' => $morning, 'Afternoon' => $afternoon] as $label => $group)
            @if ($group->isNotEmpty())
                <div>
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-base-content/45 mb-2.5">
                        {{ $label }}
                        <span class="text-base-content/30">· {{ $group->count() }}</span>
                    </p>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                        @foreach ($group as $slot)
                            <x-slot-chip as="button" :time="$slot"
                                         :state="$time === $slot ? 'chosen' : 'available'"
                                         wire:click="selectTime('{{ $slot }}')"
                                         wire:key="slot-{{ $doctor->id }}-{{ $date }}-{{ $slot }}"
                                         :aria-pressed="$time === $slot ? 'true' : 'false'" />
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Action. The readout restates the choice so nobody confirms the wrong hour. --}}
    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-base-300 pt-4">
        <p class="text-sm">
            @if ($time)
                <span class="text-base-content/50">Selected</span>
                <span class="font-mono ml-1">{{ \Carbon\Carbon::parse($date)->format('D j M') }}</span>
                <span class="font-mono">·</span>
                <span class="font-mono">{{ \Carbon\Carbon::parse($time)->format('H:i') }}–{{ \Carbon\Carbon::parse($time)->addMinutes($slotDuration)->format('H:i') }}</span>
            @else
                <span class="text-base-content/45">Pick a time to continue</span>
            @endif
        </p>

        <button type="button" wire:click="continueToBooking" @disabled(! $time)
                class="btn btn-primary rounded-field px-7 font-medium shadow-none disabled:opacity-40 {{ $standalone ? 'w-full sm:w-auto' : '' }}">
            Continue
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                <path d="M5 12h13m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                      stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</div>
