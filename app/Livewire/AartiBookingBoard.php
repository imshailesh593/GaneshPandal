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
            ->where('status', 'booked')
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
            ->where('status', 'booked')
            ->whereHas('aartiSlot', fn ($q) => $q->where('date', '>=', now()->toDateString()))
            ->exists();

        if ($hasActiveBooking) {
            session()->flash('error', 'Your family already has an upcoming aarti booking. Cancel it before booking another date.');

            return;
        }

        try {
            DB::transaction(function () use ($slotId, $member, $familyMemberIds) {
                $slot = AartiSlot::whereKey($slotId)->lockForUpdate()->first();

                if (! $slot || ! $slot->is_active || $slot->date->isPast()) {
                    session()->flash('error', 'That aarti date is not available for booking.');

                    return;
                }

                $slotTaken = AartiBooking::where('aarti_slot_id', $slot->id)
                    ->where('status', 'booked')
                    ->lockForUpdate()
                    ->exists();

                if ($slotTaken) {
                    session()->flash('error', 'That date was just booked by someone else. Please pick another.');

                    return;
                }

                $familyStillFree = ! AartiBooking::whereIn('member_id', $familyMemberIds)
                    ->where('status', 'booked')
                    ->whereHas('aartiSlot', fn ($q) => $q->where('date', '>=', now()->toDateString()))
                    ->lockForUpdate()
                    ->exists();

                if (! $familyStillFree) {
                    session()->flash('error', 'Your family already has an upcoming aarti booking. Cancel it before booking another date.');

                    return;
                }

                AartiBooking::create([
                    'member_id' => $member->id,
                    'aarti_slot_id' => $slot->id,
                    'status' => 'booked',
                ]);

                session()->flash('success', 'Aarti slot booked for '.$slot->date->format('D, j M Y').'. See you there!');
            });
        } catch (UniqueConstraintViolationException) {
            session()->flash('error', 'That date was just booked by someone else. Please pick another.');
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
                ->where('status', 'booked')
                ->lockForUpdate()
                ->first();

            if (! $booking || $booking->aartiSlot->date->isPast()) {
                session()->flash('error', 'That booking can no longer be cancelled.');

                return;
            }

            $booking->update(['status' => 'cancelled']);

            session()->flash('success', 'Booking cancelled. That date is now open for others.');
        });
    }

    protected function member(): Member
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        return $member;
    }
}
