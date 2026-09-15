<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-orange-50 font-sans text-slate-800 antialiased">
    <header class="bg-orange-600 text-white shadow-sm">
        <div class="mx-auto flex max-w-lg items-center justify-between px-4 py-3">
            <a href="{{ auth('member')->check() ? route('dashboard') : url('/') }}" class="text-base font-semibold leading-tight">
                Shakuntal Nagar<br class="sm:hidden">
                Ganeshotsav Tarun Mandal
            </a>
            @auth('member')
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-orange-700 px-3 py-1.5 text-sm font-medium hover:bg-orange-800">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-lg px-4 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
