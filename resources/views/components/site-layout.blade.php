@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' · ' . config('app.name') : config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <header class="navbar bg-base-100 border-b border-base-300 px-4 sm:px-8">
        <div class="flex-1">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 text-lg font-semibold">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border-2 border-base-content"></span>
                Clinic
            </a>
        </div>
        <div class="hidden sm:flex items-center gap-6 text-sm">
            <a href="{{ route('search') }}" wire:navigate class="link link-hover">Find a doctor</a>
            @auth('patient')
                <a href="{{ route('appointments.index') }}" wire:navigate
                   class="link link-hover {{ request()->routeIs('appointments.index') ? 'font-semibold text-primary' : '' }}">
                    My visits
                </a>
            @endauth
        </div>
        <div class="flex-none flex items-center gap-3 ml-4">
            @auth('patient')
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="avatar placeholder cursor-pointer">
                        <div class="bg-neutral text-neutral-content rounded-full w-9">
                            <span class="text-sm">{{ strtoupper(substr(auth('patient')->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 mt-3 w-52 p-2 shadow border border-base-300">
                        <li class="px-3 py-1 text-xs text-base-content/50 truncate">{{ auth('patient')->user()->email }}</li>
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
                <a href="{{ route('auth.login') }}" wire:navigate class="btn btn-outline btn-sm rounded-lg">Sign in</a>
            @endauth
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-base-300 bg-base-100 mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-8 text-sm text-base-content/60 flex flex-col sm:flex-row justify-between gap-2">
            <span>&copy; {{ now()->year }} Clinic. All rights reserved.</span>
            <span>Free cancellation &middot; Instant confirmation</span>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
