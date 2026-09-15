<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'year', 'sthapana_date', 'visarjan_date', 'aarti_time', 'is_active'])]
class Festival extends Model
{
    protected function casts(): array
    {
        return [
            'sthapana_date' => 'date',
            'visarjan_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $festival) {
            if ($festival->is_active) {
                static::where('id', '!=', $festival->id)->update(['is_active' => false]);
            }
        });
    }

    public function aartiSlots(): HasMany
    {
        return $this->hasMany(AartiSlot::class);
    }

    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Create any missing aarti slots between sthapana and visarjan dates.
     * Existing slots (including booked ones) are left untouched.
     */
    public function generateAartiSlots(): int
    {
        $created = 0;

        foreach (CarbonPeriod::create($this->sthapana_date, $this->visarjan_date) as $date) {
            $slot = $this->aartiSlots()->firstOrNew(['date' => $date->toDateString()]);

            if (! $slot->exists) {
                $slot->time = $this->aarti_time;
                $slot->is_active = true;
                $slot->save();
                $created++;
            }
        }

        return $created;
    }
}
