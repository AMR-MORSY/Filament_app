@props(['title' => null])
@php
    // Driven by APP_NAME so the site, the panels and the mail header cannot drift.
    $brand = config('app.name');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="clinic">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' · ' . $brand : $brand }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    {{-- Scroll-reveal starts hidden only when JS can bring it back. --}}
    <script>document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col bg-base-100 text-base-content antialiased">
    <header class="sticky top-0 z-40 bg-base-100/85 backdrop-blur-sm border-b border-base-300">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center gap-4 sm:gap-8">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5 shrink-0">
                <x-brand-mark class="h-7 w-7 text-primary" />
                <span class="font-display text-[1.3rem] leading-none tracking-tight whitespace-nowrap">
                    Rowan<span class="text-base-content/45"> Clinic</span>
                </span>
            </a>

            <nav class="hidden sm:flex items-center gap-7 text-sm flex-1">
                <a href="{{ route('search') }}" wire:navigate
                   class="transition-colors hover:text-primary {{ request()->routeIs('search') ? 'text-primary font-medium' : 'text-base-content/70' }}">
                    Find a time
                </a>
                @auth('patient')
                    <a href="{{ route('appointments.index') }}" wire:navigate
                       class="transition-colors hover:text-primary {{ request()->routeIs('appointments.index') ? 'text-primary font-medium' : 'text-base-content/70' }}">
                        My visits
                    </a>
                @endauth
            </nav>

            <div class="flex-1 sm:flex-none flex justify-end items-center gap-3">
                @auth('patient')
                    <div class="dropdown dropdown-end">
                        <button type="button" tabindex="0"
                                class="h-9 w-9 rounded-full bg-accent text-accent-content text-sm font-medium flex items-center justify-center transition-colors hover:bg-primary hover:text-primary-content">
                            {{ strtoupper(substr(auth('patient')->user()->name, 0, 1)) }}
                        </button>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-50 mt-3 w-56 p-2 shadow-lg border border-base-300">
                            <li class="px-3 py-1.5 text-xs text-base-content/45 truncate pointer-events-none">
                                {{ auth('patient')->user()->email }}
                            </li>
                            <li><a href="{{ route('appointments.index') }}" wire:navigate>My visits</a></li>
                            <li>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left">Sign out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('auth.login') }}" wire:navigate
                       class="text-sm text-base-content/70 hover:text-primary transition-colors">Sign in</a>
                    <a href="{{ route('search') }}" wire:navigate
                       class="btn btn-primary btn-sm rounded-field px-4 font-medium shadow-none whitespace-nowrap">Book a visit</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-base-300 bg-base-200/60 mt-20">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 flex flex-col sm:flex-row justify-between gap-6 text-sm">
            <div class="flex items-center gap-2.5 text-base-content/60">
                <x-brand-mark class="h-5 w-5 text-base-content/35" />
                <span>&copy; {{ now()->year }} {{ $brand }}</span>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-base-content/55">
                <span>Cancel free up to 24 hours before</span>
                <span>No account needed to book</span>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
