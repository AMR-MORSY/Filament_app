@props([
    'doctor',
    'size' => 'md',
    // 'circle' for inline/summary use, 'panel' for the portrait on result cards.
    'shape' => 'circle',
])
@php
    $sizes = [
        'sm' => 'h-9 w-9 text-xs',
        'md' => 'h-12 w-12 text-sm',
        'lg' => 'h-16 w-16 text-lg',
        'xl' => 'h-20 w-20 text-xl',
    ];

    $radius = $shape === 'panel' ? 'rounded-box' : 'rounded-full';

    // One renderer for every screen. Falls back to initials rather than the
    // media-library placeholder path, which has never existed on disk.
    $src = $doctor->hasMedia('main_image')
        ? $doctor->getFirstMediaUrl('main_image', 'thumb')
        : null;

    $initials = \Illuminate\Support\Str::of($doctor->name)
        ->replaceMatches('/^dr\.?\s*/i', '')
        ->squish()
        ->explode(' ')
        ->take(2)
        ->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))
        ->implode('');
@endphp

<div {{ $attributes->merge([
    'class' => 'shrink-0 overflow-hidden bg-accent text-accent-content flex items-center justify-center font-medium select-none '
        . ($sizes[$size] ?? $sizes['md']) . ' ' . $radius,
]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $doctor->name }}" loading="lazy"
             class="h-full w-full object-cover">
    @else
        <span aria-hidden="true">{{ strtoupper($initials) }}</span>
        <span class="sr-only">{{ $doctor->name }}</span>
    @endif
</div>
