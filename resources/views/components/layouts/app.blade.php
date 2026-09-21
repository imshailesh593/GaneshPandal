@props(['title' => null, 'wide' => false])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-VGT633DV9S"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-VGT633DV9S');
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-ivory-50 font-sans text-maroon-950 antialiased">
    <header class="relative bg-maroon-950 text-ivory-50">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <a href="{{ auth('member')->check() ? route('dashboard') : route('home') }}" class="font-display text-lg leading-tight text-gold-200 sm:text-xl">
                शकुंतल नगर<br class="sm:hidden">
                गणेशोत्सव तरुण मंडळ
            </a>
            @auth('member')
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md border border-gold-300/40 px-3 py-1.5 text-sm font-medium text-ivory-50 hover:bg-maroon-900">
                        लॉगआउट
                    </button>
                </form>
            @endauth
        </div>
        <x-bunting />
    </header>

    <main class="{{ $wide ? '' : 'mx-auto max-w-lg px-4 py-6' }}">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
