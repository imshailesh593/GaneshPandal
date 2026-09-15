<x-layouts.app title="Shakuntal Nagar Ganeshotsav Tarun Mandal" :wide="true">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-maroon-950 via-vermillion-700 to-marigold-500 text-ivory-50">
        <div class="pointer-events-none absolute -right-20 -top-24 h-96 w-96 rounded-full bg-marigold-400/25 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 left-6 h-72 w-72 rounded-full bg-vermillion-600/30 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:py-24 lg:grid-cols-2">
            <div class="reveal text-center lg:text-left">
                <p class="font-display text-lg text-gold-200">गणेशोत्सव {{ $festival?->year ?? now()->year }}</p>
                <h1 class="mt-3 text-3xl font-bold leading-tight text-ivory-50 sm:text-5xl">Shakuntal Nagar<br>Ganeshotsav Tarun Mandal</h1>
                <p class="mx-auto mt-5 max-w-xl text-lg text-ivory-100/90 lg:mx-0">
                    गणपती बाप्पा मोरया! चला, या उत्सवात सहभागी व्हा &mdash; रोज संध्याकाळी आरती,
                    खेळ आणि स्पर्धा, महाप्रसाद आणि बरंच काही, संपूर्ण गणेशोत्सव भर!
                </p>

                @if ($festival)
                    <div class="mx-auto mt-9 flex max-w-2xl flex-wrap items-stretch justify-center gap-4 lg:mx-0 lg:justify-start">
                        <div class="relative min-w-[9rem] flex-1 rounded-lg border border-gold-300/40 bg-maroon-900/50 px-4 py-3 backdrop-blur-sm">
                            <span class="absolute -top-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rounded-full bg-gold-300"></span>
                            <p class="text-xs uppercase tracking-wide text-gold-200">स्थापना</p>
                            <p class="mt-1 font-semibold">{{ $festival->sthapana_date->format('D, j M Y') }}</p>
                        </div>
                        <div class="relative min-w-[9rem] flex-1 rounded-lg border border-gold-300/40 bg-maroon-900/50 px-4 py-3 backdrop-blur-sm">
                            <span class="absolute -top-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rounded-full bg-gold-300"></span>
                            <p class="text-xs uppercase tracking-wide text-gold-200">रोजची आरती</p>
                            <p class="mt-1 font-semibold">{{ \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') }}</p>
                        </div>
                        <div class="relative min-w-[9rem] flex-1 rounded-lg border border-gold-300/40 bg-maroon-900/50 px-4 py-3 backdrop-blur-sm">
                            <span class="absolute -top-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rounded-full bg-gold-300"></span>
                            <p class="text-xs uppercase tracking-wide text-gold-200">विसर्जन</p>
                            <p class="mt-1 font-semibold">{{ $festival->visarjan_date->format('D, j M Y') }}</p>
                        </div>
                    </div>
                @endif

                <div class="mt-9 flex flex-col items-center gap-4 lg:items-start">
                    <div class="flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                        <a href="{{ route('login') }}" class="rounded-md bg-ivory-50 px-5 py-3 font-semibold text-vermillion-700 shadow-lg shadow-maroon-950/40 hover:bg-gold-200">
                            आरती स्लॉट बुक करा
                        </a>
                        <a href="{{ route('login') }}" class="rounded-md border border-gold-300/50 bg-white/10 px-5 py-3 font-semibold text-ivory-50 hover:bg-white/20">
                            महाप्रसादासाठी व्हॉलंटियर करा
                        </a>
                    </div>
                    <p class="max-w-md text-sm text-ivory-100/80">
                        <span class="font-semibold text-gold-200">रोज एक दिवस</span> आरतीसाठी निवडा, किंवा
                        <span class="font-semibold text-gold-200">एक दिवस</span> महाप्रसादाची जबाबदारी घ्या &mdash; लॉगिन करून ठरवा.
                    </p>
                </div>

                <nav class="mt-9 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm uppercase tracking-wide text-ivory-100/70 lg:justify-start">
                    <a href="#schedule" class="hover:text-gold-200">कार्यक्रम</a>
                    <a href="#mahaprasad" class="hover:text-gold-200">महाप्रसाद</a>
                    <a href="#gallery" class="hover:text-gold-200">फोटो गॅलरी</a>
                    <a href="#winners" class="hover:text-gold-200">विजेते</a>
                </nav>
            </div>

            <div class="reveal">
                @if ($photos->count() >= 3)
                    <div class="relative mx-auto aspect-[4/5] w-full max-w-sm sm:max-w-md">
                        <img src="{{ $photos[3]->url() }}" alt=""
                             class="absolute -left-2 top-4 z-0 h-2/5 w-2/5 rotate-[-9deg] rounded-xl border-4 border-ivory-50 object-cover shadow-xl sm:-left-6">
                        <img src="{{ $photos[6]->url() }}" alt=""
                             class="absolute -right-2 bottom-6 z-0 h-2/5 w-2/5 rotate-[8deg] rounded-xl border-4 border-ivory-50 object-cover shadow-xl sm:-right-6">
                        <img src="{{ $heroPhoto->url() }}" alt="{{ $heroPhoto->caption ?? 'श्री गणेश' }}"
                             class="absolute inset-x-[10%] top-0 z-10 h-4/5 w-4/5 -rotate-2 rounded-2xl border-4 border-gold-300 object-cover shadow-2xl">
                    </div>
                @elseif ($heroPhoto)
                    <div class="relative mx-auto w-full max-w-md">
                        <div class="overflow-hidden rounded-2xl border-4 border-gold-300 shadow-2xl">
                            <img src="{{ $heroPhoto->url() }}" alt="{{ $heroPhoto->caption ?? 'श्री गणेश' }}" class="aspect-[4/5] w-full object-cover">
                        </div>
                    </div>
                @else
                    <div class="mx-auto flex aspect-square w-full max-w-xs items-center justify-center rounded-full border-4 border-gold-300/40 text-6xl text-gold-200">
                        &#0950;
                    </div>
                @endif
            </div>
        </div>

        <x-bunting />
    </section>

    {{-- About --}}
    <section class="mx-auto max-w-3xl px-4 py-14 text-center">
        <p class="font-display text-sm uppercase tracking-widest text-vermillion-600">आमची ओळख</p>
        <h2 class="font-display mt-2 text-3xl text-maroon-950">आमचं मंडळ</h2>
        <p class="mt-4 text-lg leading-relaxed text-maroon-900/80">
            शकुंतल नगर गणेशोत्सव तरुण मंडळ आपल्या कॉलनीला
            <mark class="rounded bg-gold-200 px-1 text-maroon-950">दरवर्षी एकत्र आणतं</mark>
            &mdash; गणेशोत्सव साजरा करण्यासाठी, रोजची आरती, सांस्कृतिक स्पर्धा आणि महाप्रसादासह.
            हे सगळ्यांसाठी खुलं आहे &mdash; मेंबर्स आणि पाहुणे, सगळ्यांचं स्वागत आहे!
        </p>
    </section>

    <x-bunting class="text-gold-300" />

    {{-- Schedule --}}
    <section id="schedule" class="bg-ivory-100 py-14">
        <div class="mx-auto max-w-4xl px-4">
            <p class="text-center font-display text-sm uppercase tracking-widest text-vermillion-600">कधी, काय, कुठे</p>
            <h2 class="font-display mt-2 text-center text-3xl text-maroon-950">उत्सव कार्यक्रम</h2>

            <div class="mt-7 rounded-xl border-t-4 border-gold-300 bg-white p-4 text-center shadow-sm">
                <p class="font-medium text-vermillion-700">
                    रोजची आरती &mdash; रोज संध्याकाळी
                    {{ $festival ? \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') : '8:30 PM' }} ला
                </p>
            </div>

            <div class="mt-6 overflow-x-auto">
                @if ($games->isEmpty())
                    <p class="py-6 text-center text-maroon-900/60">खेळ आणि स्पर्धांचा कार्यक्रम लवकरच जाहीर होईल &mdash; थोडं थांबा!</p>
                @else
                    <table class="w-full min-w-[36rem] text-left text-sm">
                        <thead>
                            <tr class="border-b border-maroon-950/10 text-maroon-900/60">
                                <th class="py-2 pr-4 font-medium">तारीख</th>
                                <th class="py-2 pr-4 font-medium">वेळ</th>
                                <th class="py-2 pr-4 font-medium">खेळ / स्पर्धा</th>
                                <th class="py-2 pr-4 font-medium">ठिकाण</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($games as $game)
                                <tr class="border-b border-maroon-950/5">
                                    <td class="py-3 pr-4 whitespace-nowrap text-maroon-900/80">{{ $game->date->format('D, j M') }}</td>
                                    <td class="py-3 pr-4 whitespace-nowrap text-maroon-900/80">{{ \Illuminate\Support\Carbon::parse($game->time)->format('g:i A') }}</td>
                                    <td class="py-3 pr-4 font-medium text-maroon-950">
                                        {{ $game->name }}
                                        @if ($game->description)
                                            <p class="text-xs font-normal text-maroon-900/60">{{ $game->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4 text-maroon-900/80">{{ $game->venue ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </section>

    {{-- Mahaprasad --}}
    <section id="mahaprasad" class="bg-gradient-to-b from-white to-ivory-100 py-14">
        <div class="mx-auto max-w-4xl px-4">
            <p class="text-center font-display text-sm uppercase tracking-widest text-vermillion-600">रोज एक कुटुंब, एक सेवा</p>
            <h2 class="font-display mt-2 text-center text-3xl text-maroon-950">महाप्रसाद प्रायोजक</h2>
            <p class="mx-auto mt-3 max-w-xl text-center text-maroon-900/70">
                <mark class="rounded bg-gold-200 px-1 text-maroon-950">रोज एक कुटुंब महाप्रसाद प्रायोजित करतं.</mark>
                या उत्सवात कोण कधी प्रसाद देणार, ते इथे बघा.
            </p>

            @if ($mahaprasadSlots->isEmpty())
                <div class="mx-auto mt-7 max-w-md rounded-xl border-t-4 border-gold-300 bg-white p-6 text-center shadow-sm">
                    <p class="text-maroon-900/60">अजून कोणी महाप्रसाद प्रायोजित केला नाही &mdash; सगळे दिवस अजून खुले आहेत!</p>
                    <a href="{{ route('login') }}" class="mt-4 inline-block rounded-md bg-vermillion-600 px-4 py-2 text-sm font-semibold text-ivory-50 hover:bg-vermillion-700">
                        एक दिवस व्हॉलंटियर करा
                    </a>
                </div>
            @else
                <div class="mt-7 grid gap-4 sm:grid-cols-2">
                    @foreach ($mahaprasadSlots as $slot)
                        <div class="rounded-xl border-t-4 border-gold-300 bg-white p-4 shadow-sm">
                            <p class="font-semibold text-maroon-950">{{ $slot->date->format('D, j M Y') }}</p>
                            <p class="mt-1 text-sm text-maroon-900/70">{{ $slot->description }}</p>
                            @if ($slot->submitter)
                                <p class="mt-2 text-xs uppercase tracking-wide text-vermillion-600">प्रायोजक: {{ $slot->submitter->name }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <x-bunting :flip="true" class="text-gold-300" />

    {{-- Gallery --}}
    <section id="gallery" class="bg-maroon-950 py-14 text-ivory-50">
        <div class="mx-auto max-w-5xl px-4">
            <p class="text-center font-display text-sm uppercase tracking-widest text-gold-200">आठवणींचा खजिना</p>
            <h2 class="font-display mt-2 text-center text-3xl text-ivory-50">फोटो गॅलरी</h2>

            @if ($photos->isEmpty())
                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="flex aspect-square items-center justify-center rounded-xl border border-gold-300/30 bg-maroon-900/50 text-center text-xs text-gold-200">
                            फोटो लवकरच येणार
                        </div>
                    @endfor
                </div>
                <p class="mt-4 text-center text-sm text-ivory-100/60">डेकोरेशन आणि कार्यक्रमांचे फोटो उत्सव चालू असताना इथे अपडेट होतील.</p>
            @else
                <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                    @foreach ($photos as $i => $photo)
                        <figure class="group relative aspect-square overflow-hidden rounded-xl border-2 border-gold-300/50 bg-maroon-900 {{ $i % 3 === 0 ? '-rotate-1' : ($i % 3 === 1 ? 'rotate-1' : '') }}">
                            <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?? 'गणेशोत्सव फोटो' }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @if ($photo->caption)
                                <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-maroon-950/90 to-transparent px-2 py-2 text-xs text-ivory-50 opacity-0 transition group-hover:opacity-100">
                                    {{ $photo->caption }}
                                </figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <x-bunting class="text-gold-300" />

    {{-- Winners --}}
    <section id="winners" class="bg-ivory-100 py-14">
        <div class="mx-auto max-w-4xl px-4">
            <p class="text-center font-display text-sm uppercase tracking-widest text-vermillion-600">मानाचं पान</p>
            <h2 class="font-display mt-2 text-center text-3xl text-maroon-950">विजेते</h2>

            @if ($winners->isEmpty())
                <p class="mt-7 text-center text-maroon-900/60">प्रत्येक स्पर्धेनंतर विजेत्यांची नावं इथे जाहीर होतील.</p>
            @else
                <div class="mt-7 space-y-4">
                    @foreach ($winners as $game)
                        <div class="rounded-xl border-t-4 border-gold-300 bg-white p-4 shadow-sm">
                            <p class="font-semibold text-maroon-950">{{ $game->name }} <span class="text-sm font-normal text-maroon-900/50">&middot; {{ $game->date->format('j M Y') }}</span></p>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach ($game->participants as $participant)
                                    <li class="flex items-center gap-2">
                                        <span class="inline-flex w-24 shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-medium
                                            {{ match ($participant->position) {
                                                'first' => 'bg-gold-200 text-vermillion-700',
                                                'second' => 'bg-slate-200 text-slate-700',
                                                'third' => 'bg-marigold-400/30 text-vermillion-700',
                                                default => 'bg-emerald-700/10 text-emerald-700',
                                            } }}">
                                            {{ match ($participant->position) {
                                                'first' => 'पहिला नंबर',
                                                'second' => 'दुसरा नंबर',
                                                'third' => 'तिसरा नंबर',
                                                default => 'सहभागी',
                                            } }}
                                        </span>
                                        <span class="text-maroon-900/80">{{ $participant->displayName() }}</span>
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
    <section class="relative overflow-hidden bg-gradient-to-br from-vermillion-700 to-maroon-950 py-12 text-center text-ivory-50">
        <div class="pointer-events-none absolute -right-10 -top-10 h-64 w-64 rounded-full bg-marigold-400/20 blur-3xl"></div>
        <h2 class="font-display relative text-2xl text-gold-200">या, उत्सवात सहभागी व्हा</h2>
        <p class="relative mt-2 text-ivory-100/90">तुमचा मोबाईल नंबर टाकून आरती स्लॉट बुक करा किंवा महाप्रसाद प्रायोजित करा.</p>
        <a href="{{ route('login') }}" class="relative mt-5 inline-block rounded-md bg-ivory-50 px-5 py-3 font-semibold text-vermillion-700 shadow-lg hover:bg-gold-200">
            मेंबर लॉगिन
        </a>
    </section>

    <footer class="bg-maroon-950 py-6 text-center text-sm text-ivory-100/50">
        &copy; {{ now()->year }} Shakuntal Nagar Ganeshotsav Tarun Mandal
    </footer>
</x-layouts.app>
