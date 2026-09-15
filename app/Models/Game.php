<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'date', 'time', 'venue', 'description'])]
class Game extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function participants(): HasMany
    {
        return $this->hasMany(GameParticipant::class);
    }
}
