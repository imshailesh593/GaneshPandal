<x-layouts.app title="Shakuntal Nagar Ganeshotsav Tarun Mandal" :wide="true">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-orange-600 via-orange-500 to-amber-500 text-white">
        <svg class="pointer-events-none absolute -right-16 -top-16 h-72 w-72 opacity-20" viewBox="0 0 200 200" fill="none">
            <circle cx="100" cy="100" r="90" fill="currentColor" />
        </svg>
        <svg class="pointer-events-none absolute -bottom-10 left-1/4 h-40 w-40 opacity-10" viewBox="0 0 200 200" fill="none">
            <circle cx="100" cy="100" r="90" fill="currentColor" />
        </svg>

        <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-4 py-14 sm:py-20 lg:grid-cols-2">
            <div class="text-center lg:text-left">
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-100">Ganeshotsav {{ $festival?->year ?? now()->year }}</p>
                <h1 class="mt-2 text-3xl font-bold leading-tight sm:text-5xl">Shakuntal Nagar<br>Ganeshotsav Tarun Mandal</h1>
                <p class="mx-auto mt-4 max-w-xl text-orange-50 lg:mx-0">
                    Ganpati Bappa Morya! Join our colony in celebrating devotion, culture and community
                    &mdash; every evening aarti, games, mahaprasad and more, all festival long.
                </p>

                @if ($festival)
                    <div class="mx-auto mt-8 flex max-w-2xl flex-wrap items-stretch justify-center gap-3 lg:mx-0 lg:justify-start">
                        <div class="min-w-[9rem] flex-1 rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-wide text-amber-100">Sthapana</p>
                            <p class="mt-1 font-semibold">{{ $festival->sthapana_date->format('D, j M Y') }}</p>
                        </div>
                        <div class="min-w-[9rem] flex-1 rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-wide text-amber-100">Daily Aarti</p>
                            <p class="mt-1 font-semibold">{{ \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') }}</p>
                        </div>
                        <div class="min-w-[9rem] flex-1 rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-wide text-amber-100">Visarjan</p>
                            <p class="mt-1 font-semibold">{{ $festival->visarjan_date->format('D, j M Y') }}</p>
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                    <a href="{{ route('login') }}" class="rounded-md bg-white px-5 py-3 font-medium text-orange-700 shadow-sm hover:bg-orange-50">
                    Book Aarti Slot
                </a>
                <a href="{{ route('login') }}" class="rounded-md bg-orange-800/40 px-5 py-3 font-medium text-white ring-1 ring-inset ring-white/40 hover:bg-orange-800/60">
                    Volunteer for Mahaprasad
                </a>
                </div>

                <nav class="mt-10 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-orange-50 lg:justify-start">
                    <a href="#schedule" class="hover:text-white hover:underline">Schedule</a>
                    <a href="#mahaprasad" class="hover:text-white hover:underline">Mahaprasad</a>
                    <a href="#gallery" class="hover:text-white hover:underline">Gallery</a>
                    <a href="#winners" class="hover:text-white hover:underline">Winners</a>
                </nav>
            </div>

            @if ($heroPhoto)
                <div class="relative mx-auto w-full max-w-md">
                    <div class="overflow-hidden rounded-2xl shadow-2xl ring-4 ring-white/20">
                        <img src="{{ $heroPhoto->url() }}" alt="{{ $heroPhoto->caption ?? 'Shri Ganesh' }}" class="aspect-[4/5] w-full object-cover">
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- About --}}
    <section class="mx-auto max-w-3xl px-4 py-12 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Our Mandal</h2>
        <p class="mt-3 text-slate-600">
            Shakuntal Nagar Ganeshotsav Tarun Mandal brings our colony together every year to celebrate
            Ganeshotsav with daily aarti, cultural competitions and community mahaprasad &mdash; open to
            everyone in the neighbourhood, members and guests alike.
        </p>
    </section>

    {{-- Schedule --}}
    <section id="schedule" class="bg-white py-12">
        <div class="mx-auto max-w-4xl px-4">
            <h2 class="text-center text-2xl font-bold text-slate-900">Events Schedule</h2>

            <div class="mt-6 rounded-xl bg-orange-50 p-4 text-center">
                <p class="font-medium text-orange-800">
                    Daily Aarti &mdash; every evening at
                    {{ $festival ? \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') : '8:30 PM' }}
                </p>
            </div>

            <div class="mt-6 overflow-x-auto">
                @if ($games->isEmpty())
                    <p class="py-6 text-center text-slate-500">Games &amp; competitions schedule will be published soon.</p>
                @else
                    <table class="w-full min-w-[36rem] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500">
                                <th class="py-2 pr-4 font-medium">Date</th>
                                <th class="py-2 pr-4 font-medium">Time</th>
                                <th class="py-2 pr-4 font-medium">Game / Competition</th>
                                <th class="py-2 pr-4 font-medium">Venue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($games as $game)
                                <tr class="border-b border-slate-100">
                                    <td class="py-3 pr-4 whitespace-nowrap text-slate-700">{{ $game->date->format('D, j M') }}</td>
                                    <td class="py-3 pr-4 whitespace-nowrap text-slate-700">{{ \Illuminate\Support\Carbon::parse($game->time)->format('g:i A') }}</td>
                                    <td class="py-3 pr-4 font-medium text-slate-900">
                                        {{ $game->name }}
                                        @if ($game->description)
                                            <p class="text-xs font-normal text-slate-500">{{ $game->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4 text-slate-700">{{ $game->venue ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </section>

    {{-- Mahaprasad --}}
    <section id="mahaprasad" class="bg-orange-50/60 py-12">
        <div class="mx-auto max-w-4xl px-4">
            <h2 class="text-center text-2xl font-bold text-slate-900">Mahaprasad Sponsors</h2>
            <p class="mx-auto mt-2 max-w-xl text-center text-slate-600">
                One family sponsors mahaprasad each day. Here's who's serving prasad this festival.
            </p>

            @if ($mahaprasadSlots->isEmpty())
                <div class="mx-auto mt-6 max-w-md rounded-xl bg-white p-6 text-center shadow-sm">
                    <p class="text-slate-500">No mahaprasad sponsors confirmed yet &mdash; every day is still open!</p>
                    <a href="{{ route('login') }}" class="mt-4 inline-block rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">
                        Volunteer for a day
                    </a>
                </div>
            @else
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach ($mahaprasadSlots as $slot)
                        <div class="rounded-xl bg-white p-4 shadow-sm">
                            <p class="font-semibold text-slate-900">{{ $slot->date->format('D, j M Y') }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $slot->description }}</p>
                            @if ($slot->submitter)
                                <p class="mt-2 text-xs uppercase tracking-wide text-orange-600">Sponsored by {{ $slot->submitter->name }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Gallery --}}
    <section id="gallery" class="bg-white py-12">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-center text-2xl font-bold text-slate-900">Gallery</h2>

            @if ($photos->isEmpty())
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="flex aspect-square items-center justify-center rounded-xl bg-gradient-to-br from-orange-100 to-amber-100 text-center text-xs text-orange-400">
                            Photos coming soon
                        </div>
                    @endfor
                </div>
                <p class="mt-4 text-center text-sm text-slate-500">Decoration and event photos will be added here during the festival.</p>
            @else
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    @foreach ($photos as $photo)
                        <figure class="group relative aspect-square overflow-hidden rounded-xl bg-slate-100">
                            <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?? 'Ganeshotsav photo' }}" class="h-full w-full object-cover">
                            @if ($photo->caption)
                                <figcaption class="absolute inset-x-0 bottom-0 bg-black/50 px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100">
                                    {{ $photo->caption }}
                                </figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Winners --}}
    <section id="winners" class="bg-orange-50/60 py-12">
        <div class="mx-auto max-w-4xl px-4">
            <h2 class="text-center text-2xl font-bold text-slate-900">Winners</h2>

            @if ($winners->isEmpty())
                <p class="mt-6 text-center text-slate-500">Winners will be announced here after each event.</p>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($winners as $game)
                        <div class="rounded-xl bg-white p-4 shadow-sm">
                            <p class="font-semibold text-slate-900">{{ $game->name }} <span class="text-sm font-normal text-slate-500">&middot; {{ $game->date->format('j M Y') }}</span></p>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach ($game->participants as $participant)
                                    <li class="flex items-center gap-2">
                                        <span class="inline-flex w-24 shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-medium
                                            {{ match ($participant->position) {
                                                'first' => 'bg-amber-100 text-amber-800',
                                                'second' => 'bg-slate-200 text-slate-700',
                                                'third' => 'bg-orange-100 text-orange-700',
                                                default => 'bg-green-100 text-green-700',
                                            } }}">
                                            {{ match ($participant->position) {
                                                'first' => '1st place',
                                                'second' => '2nd place',
                                                'third' => '3rd place',
                                                default => 'Participant',
                                            } }}
                                        </span>
                                        <span class="text-slate-700">{{ $participant->displayName() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Footer CTA --}}
    <section class="bg-orange-600 py-10 text-center text-white">
        <h2 class="text-xl font-semibold">Be part of the celebration</h2>
        <p class="mt-2 text-orange-50">Log in with your mobile number to book an aarti slot or sponsor mahaprasad.</p>
        <a href="{{ route('login') }}" class="mt-5 inline-block rounded-md bg-white px-5 py-3 font-medium text-orange-700 hover:bg-orange-50">
            Member Login
        </a>
    </section>

    <footer class="bg-slate-900 py-6 text-center text-sm text-slate-400">
        &copy; {{ now()->year }} Shakuntal Nagar Ganeshotsav Tarun Mandal
    </footer>
</x-layouts.app>
