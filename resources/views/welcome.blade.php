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
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-100">गणेशोत्सव {{ $festival?->year ?? now()->year }}</p>
                <h1 class="mt-2 text-3xl font-bold leading-tight sm:text-5xl">Shakuntal Nagar<br>Ganeshotsav Tarun Mandal</h1>
                <p class="mx-auto mt-4 max-w-xl text-orange-50 lg:mx-0">
                    गणपती बाप्पा मोरया! चला, या उत्सवात सहभागी व्हा &mdash; रोज संध्याकाळी आरती,
                    खेळ-स्पर्धा, महाप्रसाद आणि बरंच काही, संपूर्ण गणेशोत्सव भर!
                </p>

                @if ($festival)
                    <div class="mx-auto mt-8 flex max-w-2xl flex-wrap items-stretch justify-center gap-3 lg:mx-0 lg:justify-start">
                        <div class="min-w-[9rem] flex-1 rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-wide text-amber-100">स्थापना</p>
                            <p class="mt-1 font-semibold">{{ $festival->sthapana_date->format('D, j M Y') }}</p>
                        </div>
                        <div class="min-w-[9rem] flex-1 rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-wide text-amber-100">रोजची आरती</p>
                            <p class="mt-1 font-semibold">{{ \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') }}</p>
                        </div>
                        <div class="min-w-[9rem] flex-1 rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                            <p class="text-xs uppercase tracking-wide text-amber-100">विसर्जन</p>
                            <p class="mt-1 font-semibold">{{ $festival->visarjan_date->format('D, j M Y') }}</p>
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                    <a href="{{ route('login') }}" class="rounded-md bg-white px-5 py-3 font-medium text-orange-700 shadow-sm hover:bg-orange-50">
                        आरती स्लॉट बुक करा
                    </a>
                    <a href="{{ route('login') }}" class="rounded-md bg-orange-800/40 px-5 py-3 font-medium text-white ring-1 ring-inset ring-white/40 hover:bg-orange-800/60">
                        महाप्रसादासाठी व्हॉलंटियर करा
                    </a>
                </div>

                <nav class="mt-10 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-orange-50 lg:justify-start">
                    <a href="#schedule" class="hover:text-white hover:underline">कार्यक्रम</a>
                    <a href="#mahaprasad" class="hover:text-white hover:underline">महाप्रसाद</a>
                    <a href="#gallery" class="hover:text-white hover:underline">फोटो गॅलरी</a>
                    <a href="#winners" class="hover:text-white hover:underline">विजेते</a>
                </nav>
            </div>

            @if ($heroPhoto)
                <div class="relative mx-auto w-full max-w-md">
                    <div class="overflow-hidden rounded-2xl shadow-2xl ring-4 ring-white/20">
                        <img src="{{ $heroPhoto->url() }}" alt="{{ $heroPhoto->caption ?? 'श्री गणेश' }}" class="aspect-[4/5] w-full object-cover">
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- About --}}
    <section class="mx-auto max-w-3xl px-4 py-12 text-center">
        <h2 class="text-2xl font-bold text-slate-900">आमचं मंडळ</h2>
        <p class="mt-3 text-slate-600">
            शकुंतल नगर गणेशोत्सव तरुण मंडळ आपल्या कॉलनीला दरवर्षी एकत्र आणतं &mdash; गणेशोत्सव साजरा
            करण्यासाठी, रोजची आरती, सांस्कृतिक स्पर्धा आणि महाप्रसादासह. हे सगळ्यांसाठी खुलं आहे
            &mdash; मेंबर्स आणि पाहुणे, सगळ्यांचं स्वागत आहे!
        </p>
    </section>

    {{-- Schedule --}}
    <section id="schedule" class="bg-white py-12">
        <div class="mx-auto max-w-4xl px-4">
            <h2 class="text-center text-2xl font-bold text-slate-900">उत्सव कार्यक्रम</h2>

            <div class="mt-6 rounded-xl bg-orange-50 p-4 text-center">
                <p class="font-medium text-orange-800">
                    रोजची आरती &mdash; रोज संध्याकाळी
                    {{ $festival ? \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') : '8:30 PM' }} ला
                </p>
            </div>

            <div class="mt-6 overflow-x-auto">
                @if ($games->isEmpty())
                    <p class="py-6 text-center text-slate-500">खेळ-स्पर्धांचा कार्यक्रम लवकरच जाहीर होईल &mdash; थोडं थांबा!</p>
                @else
                    <table class="w-full min-w-[36rem] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500">
                                <th class="py-2 pr-4 font-medium">तारीख</th>
                                <th class="py-2 pr-4 font-medium">वेळ</th>
                                <th class="py-2 pr-4 font-medium">खेळ / स्पर्धा</th>
                                <th class="py-2 pr-4 font-medium">ठिकाण</th>
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
            <h2 class="text-center text-2xl font-bold text-slate-900">महाप्रसाद प्रायोजक</h2>
            <p class="mx-auto mt-2 max-w-xl text-center text-slate-600">
                रोज एक कुटुंब महाप्रसाद प्रायोजित करतं. या उत्सवात कोण कधी प्रसाद देणार, ते इथे बघा.
            </p>

            @if ($mahaprasadSlots->isEmpty())
                <div class="mx-auto mt-6 max-w-md rounded-xl bg-white p-6 text-center shadow-sm">
                    <p class="text-slate-500">अजून कोणी महाप्रसाद प्रायोजित केला नाही &mdash; सगळे दिवस अजून खुले आहेत!</p>
                    <a href="{{ route('login') }}" class="mt-4 inline-block rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">
                        एक दिवस व्हॉलंटियर करा
                    </a>
                </div>
            @else
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach ($mahaprasadSlots as $slot)
                        <div class="rounded-xl bg-white p-4 shadow-sm">
                            <p class="font-semibold text-slate-900">{{ $slot->date->format('D, j M Y') }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $slot->description }}</p>
                            @if ($slot->submitter)
                                <p class="mt-2 text-xs uppercase tracking-wide text-orange-600">प्रायोजक: {{ $slot->submitter->name }}</p>
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
            <h2 class="text-center text-2xl font-bold text-slate-900">फोटो गॅलरी</h2>

            @if ($photos->isEmpty())
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="flex aspect-square items-center justify-center rounded-xl bg-gradient-to-br from-orange-100 to-amber-100 text-center text-xs text-orange-400">
                            फोटो लवकरच येणार
                        </div>
                    @endfor
                </div>
                <p class="mt-4 text-center text-sm text-slate-500">डेकोरेशन आणि कार्यक्रमांचे फोटो उत्सव चालू असताना इथे अपडेट होतील.</p>
            @else
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    @foreach ($photos as $photo)
                        <figure class="group relative aspect-square overflow-hidden rounded-xl bg-slate-100">
                            <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?? 'गणेशोत्सव फोटो' }}" class="h-full w-full object-cover">
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
            <h2 class="text-center text-2xl font-bold text-slate-900">विजेते</h2>

            @if ($winners->isEmpty())
                <p class="mt-6 text-center text-slate-500">प्रत्येक स्पर्धेनंतर विजेत्यांची नावं इथे जाहीर होतील.</p>
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
                                                'first' => 'पहिला नंबर',
                                                'second' => 'दुसरा नंबर',
                                                'third' => 'तिसरा नंबर',
                                                default => 'सहभागी',
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
        <h2 class="text-xl font-semibold">या, उत्सवात सहभागी व्हा</h2>
        <p class="mt-2 text-orange-50">तुमचा मोबाईल नंबर टाकून आरती स्लॉट बुक करा किंवा महाप्रसाद प्रायोजित करा.</p>
        <a href="{{ route('login') }}" class="mt-5 inline-block rounded-md bg-white px-5 py-3 font-medium text-orange-700 hover:bg-orange-50">
            मेंबर लॉगिन
        </a>
    </section>

    <footer class="bg-slate-900 py-6 text-center text-sm text-slate-400">
        &copy; {{ now()->year }} Shakuntal Nagar Ganeshotsav Tarun Mandal
    </footer>
</x-layouts.app>
