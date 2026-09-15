<div class="space-y-4">
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Logged in as</p>
        <p class="font-semibold text-slate-900">{{ auth('member')->user()->name }}</p>
        <p class="text-sm text-slate-500">{{ auth('member')->user()->family->name }} family</p>
    </div>

    @if (session('success'))
        <div class="rounded-md bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    @if (! $festival)
        <div class="rounded-xl bg-white p-4 text-sm text-slate-500 shadow-sm">
            No festival is set up for booking yet. Please check back soon.
        </div>
    @else
        <div class="rounded-xl bg-orange-100 p-4 text-sm text-orange-800">
            <p class="font-medium">{{ $festival->name }}</p>
            <p>Daily aarti at {{ \Illuminate\Support\Carbon::parse($festival->aarti_time)->format('g:i A') }}</p>
        </div>

        @if ($myActiveBooking)
            <div class="rounded-xl border border-orange-300 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Your family's upcoming aarti</p>
                <p class="text-lg font-semibold text-slate-900">
                    {{ $myActiveBooking->aartiSlot->date->format('l, j F Y') }}
                </p>
                <button
                    wire:click="cancel({{ $myActiveBooking->id }})"
                    wire:confirm="Cancel this booking? The date will open up for other families."
                    class="mt-3 w-full rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200"
                >
                    Cancel booking
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
                        <p class="font-medium text-slate-900">{{ $slot->date->format('D, j M Y') }}</p>
                        @if ($booking)
                            <p class="text-sm text-slate-500">
                                Booked{{ $isMine ? ' by your family' : ' — ' . $booking->member->family->name . ' family' }}
                            </p>
                        @elseif ($isPast)
                            <p class="text-sm text-slate-400">Past</p>
                        @elseif (! $slot->is_active)
                            <p class="text-sm text-slate-400">Not available</p>
                        @else
                            <p class="text-sm text-green-600">Available</p>
                        @endif
                    </div>

                    @if (! $booking && ! $isPast && $slot->is_active && ! $myActiveBooking)
                        <button
                            wire:click="book({{ $slot->id }})"
                            wire:loading.attr="disabled"
                            class="rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 disabled:opacity-50"
                        >
                            Book
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
