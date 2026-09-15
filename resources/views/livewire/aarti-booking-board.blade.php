<div class="space-y-4">
    <div class="rounded-xl border-t-4 border-gold-300 bg-white p-4 shadow-sm">
        <p class="text-sm text-maroon-900/50">लॉगिन आहे</p>
        <p class="font-semibold text-maroon-950">{{ auth('member')->user()->name }}</p>
        <p class="text-sm text-maroon-900/50">{{ auth('member')->user()->family->name }} कुटुंब</p>
    </div>

    @if (session('success'))
        <div class="rounded-md bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    @if (! $festival)
        <div class="rounded-xl bg-white p-4 text-sm text-maroon-900/50 shadow-sm">
            अजून बुकिंगसाठी उत्सव सेट केलेला नाही. लवकरच परत बघा.
        </div>
    @else
        <div class="rounded-xl bg-gradient-to-br from-vermillion-600 to-marigold-500 p-4 text-sm text-ivory-50 shadow-sm">
            <p class="font-semibold">{{ $festival->name }}</p>
            <p class="text-ivory-100/90">रोजची आरती {{ \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') }} ला</p>
        </div>

        @if ($myActiveBooking)
            <div class="rounded-xl border border-gold-300 bg-white p-4 shadow-sm">
                <p class="text-sm text-maroon-900/50">तुमच्या कुटुंबाची पुढची आरती</p>
                <p class="text-lg font-semibold text-maroon-950">
                    {{ $myActiveBooking->aartiSlot->date->format('l, j F Y') }}
                </p>
                <button
                    wire:click="cancel({{ $myActiveBooking->id }})"
                    wire:confirm="ही बुकिंग कॅन्सल करायची? ही तारीख इतर कुटुंबांसाठी खुली होईल."
                    class="mt-3 w-full rounded-md bg-ivory-100 px-4 py-2 text-sm font-medium text-maroon-900 hover:bg-gold-200"
                >
                    बुकिंग कॅन्सल करा
                </button>
            </div>
        @endif

        <div class="space-y-2">
            @foreach ($slots as $slot)
                @php
                    $isPast = $slot->date->isPast();
                    $booking = $slot->activeBooking;
                    $isMine = $booking && $familyMemberIds->contains($booking->member_id);
                @endphp
                <div class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm {{ $isPast ? 'opacity-50' : '' }}">
                    <div>
                        <p class="font-medium text-maroon-950">{{ $slot->date->format('D, j M Y') }}</p>
                        @if ($booking)
                            <p class="text-sm text-maroon-900/50">
                                {{ $isMine ? 'तुमच्या कुटुंबाने बुक केलं' : $booking->member->family->name . ' कुटुंबाने बुक केलं' }}
                            </p>
                        @elseif ($isPast)
                            <p class="text-sm text-maroon-900/30">संपली</p>
                        @elseif (! $slot->is_active)
                            <p class="text-sm text-maroon-900/30">उपलब्ध नाही</p>
                        @else
                            <p class="text-sm text-emerald-700">उपलब्ध आहे</p>
                        @endif
                    </div>

                    @if (! $booking && ! $isPast && $slot->is_active && ! $myActiveBooking)
                        <button
                            wire:click="book({{ $slot->id }})"
                            wire:loading.attr="disabled"
                            class="rounded-md bg-vermillion-600 px-4 py-2 text-sm font-medium text-white hover:bg-vermillion-700 disabled:opacity-50"
                        >
                            बुक करा
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
