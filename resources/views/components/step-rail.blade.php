@props([
    // 1 = find, 2 = choose a time, 3 = your details
    'step' => 1,
    'date' => null,
    'time' => null,
    'duration' => null,
])
@php
    $step = (int) $step;

    $steps = [
        1 => 'Find',
        2 => 'Choose a time',
        3 => 'Your details',
    ];

    // The readout is the appointment taking shape: it gains the day at step 2
    // and the hour at step 3, so the rail reports real state rather than
    // decorating the page with numbers.
    $readout = '—:—';

    if ($date) {
        $readout = \Carbon\Carbon::parse($date)->format('D j M');
    }

    if ($date && $time) {
        $start = \Carbon\Carbon::parse($time);
        $readout .= ' · ' . $start->format('H:i');

        if ($duration) {
            $readout .= '–' . $start->copy()->addMinutes((int) $duration)->format('H:i');
        }
    }

    $settled = $date && $time;
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-x-3 gap-y-4 justify-between']) }}>
    <ol class="flex items-center gap-2 sm:gap-3">
        @foreach ($steps as $index => $label)
            @php
                $isDone = $index < $step;
                $isCurrent = $index === $step;
            @endphp

            @if (! $loop->first)
                <li aria-hidden="true"
                    class="h-px w-5 sm:w-8 {{ $isDone || $isCurrent ? 'bg-primary/40' : 'bg-base-300' }}"></li>
            @endif

            <li class="flex items-center gap-2" @if ($isCurrent) aria-current="step" @endif>
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border font-mono text-[0.7rem] leading-none
                    {{ $isDone ? 'border-primary bg-primary text-primary-content' : '' }}
                    {{ $isCurrent ? 'border-primary text-primary' : '' }}
                    {{ ! $isDone && ! $isCurrent ? 'border-base-300 text-base-content/35' : '' }}">
                    @if ($isDone)
                        <svg viewBox="0 0 12 12" class="h-3 w-3" fill="none" aria-hidden="true">
                            <path d="m2.5 6.2 2.3 2.3 4.7-4.9" stroke="currentColor" stroke-width="1.8"
                                  stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="sr-only">Done</span>
                    @else
                        {{ $index }}
                    @endif
                </span>
                <span class="text-sm whitespace-nowrap
                    {{ $isCurrent ? 'text-base-content font-medium' : 'text-base-content/45' }}
                    {{ ! $isCurrent ? 'hidden sm:inline' : '' }}">
                    {{ $label }}
                </span>
            </li>
        @endforeach
    </ol>

    <div class="flex items-center gap-2 text-sm">
        <span class="text-base-content/40 hidden sm:inline">Your slot</span>
        <span class="inline-flex items-center rounded-field border px-2.5 py-1.5 font-mono text-sm leading-none tabular-nums transition-colors
            {{ $settled ? 'border-primary bg-primary text-primary-content' : 'border-base-300 bg-base-200 text-base-content/45' }}">
            {{ $readout }}
        </span>
    </div>
</div>
