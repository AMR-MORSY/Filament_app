@props([
    // "09:30" or "09:30:00" — anything Carbon can parse.
    'time',
    // available | chosen | taken
    'state' => 'available',
    // Minutes. When given, the chip renders a range instead of a single time.
    'duration' => null,
    // Rendered element. Pass 'button' to make it pressable.
    'as' => 'span',
    'size' => 'md',
])
@php
    $start = \Carbon\Carbon::parse($time);
    $label = $start->format('H:i');

    if ($duration) {
        $label .= '–' . $start->copy()->addMinutes((int) $duration)->format('H:i');
    }

    // Fixed widths, not padding. Poppins has no monospace cut, so a column of
    // times only stays aligned if every chip occupies the same box.
    $sizes = [
        'sm' => 'px-2 py-1 text-[0.72rem] '.($duration ? 'min-w-[6.5rem]' : 'min-w-[3.25rem]'),
        'md' => 'px-2.5 py-1.5 text-sm '.($duration ? 'min-w-[7.5rem]' : 'min-w-[3.75rem]'),
        'lg' => 'px-4 py-2.5 text-lg '.($duration ? 'min-w-[10rem]' : 'min-w-[5rem]'),
    ];

    // The one place colour is spent on this site.
    $states = [
        'available' => 'bg-accent text-accent-content border-transparent hover:border-primary/40 hover:bg-primary/12',
        'chosen' => 'bg-primary text-primary-content border-primary shadow-sm',
        'taken' => 'bg-base-100 text-base-content/30 border-base-300 line-through',
    ];

    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-field border font-mono font-medium leading-none tabular-nums',
        'transition-[background-color,color,border-color,transform] duration-150 ease-out',
        $sizes[$size] ?? $sizes['md'],
        $states[$state] ?? $states['available'],
        $as === 'button' && $state !== 'taken' ? 'cursor-pointer active:scale-[0.96]' : '',
        $as === 'button' && $state === 'taken' ? 'cursor-not-allowed' : '',
    ]);
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }} @if ($as === 'button') type="button" @endif>
    {{ $label }}
</{{ $as }}>
