<?php

namespace App\Livewire;

use App\Models\AartiBooking;
use App\Models\AartiSlot;
use App\Models\Festival;
use App\Models\Member;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AartiBookingBoard extends Component
{
    public function render()
    {
        $member = $this->member();
        $festival = Festival::active();

        $slots = $festival
            ? $festival->aartiSlots()
                ->with('activeBooking.member.family')
                ->orderBy('date')
                ->get()
            : collect();

        $familyMemberIds = Member::where('family_id', $member->family_id)->pluck('id');

        $myActiveBooking = AartiBooking::with('aartiSlot')
            ->whereIn('member_id', $familyMemberIds)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('aartiSlot', fn ($q) => $q->where('date', '>=', now()->toDateString()))
            ->first();

        return view('livewire.aarti-booking-board', [
            'festival' => $festival,
            'slots' => $slots,
            'myActiveBooking' => $myActiveBooking,
            'familyMemberIds' => $familyMemberIds,
        ]);
    }

    public function book(int $slotId): void
    {
        $member = $this->member();
        $familyMemberIds = Member::where('family_id', $member->family_id)->pluck('id');

        $hasActiveBooking = AartiBooking::whereIn('member_id', $familyMemberIds)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('aartiSlot', fn ($q) => $q->where('date', '>=', now()->toDateString()))
            ->exists();

        if ($hasActiveBooking) {
            session()->flash('error', 'तुमच्या कुटुंबाची आधीच एक आरती बुकिंग आहे. दुसरी तारीख बुक करण्याआधी ती कॅन्सल करा.');

            return;
        }

        try {
            DB::transaction(function () use ($slotId, $member, $familyMemberIds) {
                $slot = AartiSlot::whereKey($slotId)->lockForUpdate()->first();

                if (! $slot || ! $slot->is_active || $slot->date->isPast()) {
                    session()->flash('error', 'ही आरतीची तारीख बुकिंगसाठी उपलब्ध नाही.');

                    return;
                }

                $slotTaken = AartiBooking::where('aarti_slot_id', $slot->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->lockForUpdate()
                    ->exists();

                if ($slotTaken) {
                    session()->flash('error', 'ही तारीख आत्ताच दुसऱ्या कोणी बुक केली. दुसरी तारीख निवडा.');

                    return;
                }

                $familyStillFree = ! AartiBooking::whereIn('member_id', $familyMemberIds)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereHas('aartiSlot', fn ($q) => $q->where('date', '>=', now()->toDateString()))
                    ->lockForUpdate()
                    ->exists();

                if (! $familyStillFree) {
                    session()->flash('error', 'तुमच्या कुटुंबाची आधीच एक आरती बुकिंग आहे. दुसरी तारीख बुक करण्याआधी ती कॅन्सल करा.');

                    return;
                }

                AartiBooking::create([
                    'member_id' => $member->id,
                    'aarti_slot_id' => $slot->id,
                    'status' => 'pending',
                ]);

                session()->flash('success', 'आरती स्लॉट '.$slot->date->format('D, j M Y').' साठी विनंती केली! अ‍ॅडमिन कन्फर्मेशनची वाट आहे.');
            });
        } catch (UniqueConstraintViolationException) {
            session()->flash('error', 'ही तारीख आत्ताच दुसऱ्या कोणी बुक केली. दुसरी तारीख निवडा.');
        }
    }

    public function cancel(int $bookingId): void
    {
        $member = $this->member();
        $familyMemberIds = Member::where('family_id', $member->family_id)->pluck('id');

        DB::transaction(function () use ($bookingId, $familyMemberIds) {
            $booking = AartiBooking::with('aartiSlot')
                ->whereKey($bookingId)
                ->whereIn('member_id', $familyMemberIds)
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->first();

            if (! $booking || $booking->aartiSlot->date->isPast()) {
                session()->flash('error', 'ही बुकिंग आता कॅन्सल करता येणार नाही.');

                return;
            }

            $booking->update(['status' => 'cancelled']);

            session()->flash('success', 'बुकिंग कॅन्सल झालं. ही तारीख आता इतरांसाठी खुली आहे.');
        });
    }

    protected function member(): Member
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        return $member;
    }
}
