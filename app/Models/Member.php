<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'mobile', 'firebase_uid', 'family_id'])]
class Member extends Authenticatable
{
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function aartiBookings(): HasMany
    {
        return $this->hasMany(AartiBooking::class);
    }
}
