<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'guest_name', 'aarti_slot_id', 'status'])]
class AartiBooking extends Model
{
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function aartiSlot(): BelongsTo
    {
        return $this->belongsTo(AartiSlot::class);
    }

    /**
     * Who this booking is for: the member's name, or the guest/chief name
     * an admin entered on their behalf.
     */
    public function displayName(): string
    {
        return $this->member?->name ?? $this->guest_name ?? 'Guest';
    }

    /**
     * Short label for "who booked this" shown to other members: the plot
     * number for a member booking, or the guest/chief name for an admin
     * booking made on someone's behalf.
     */
    public function bookedByLabel(): string
    {
        if ($this->member) {
            return 'प्लॉट '.$this->member->family->plot_number;
        }

        return $this->guest_name ?? 'मंडळ';
    }
}
