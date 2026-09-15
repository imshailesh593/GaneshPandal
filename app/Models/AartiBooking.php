<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'aarti_slot_id', 'status'])]
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
}
