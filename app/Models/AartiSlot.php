<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['festival_id', 'date', 'time', 'is_active'])]
class AartiSlot extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(AartiBooking::class);
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(AartiBooking::class)->where('status', 'booked');
    }

    public function isBooked(): bool
    {
        return $this->activeBooking !== null;
    }
}
