<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['date', 'description', 'submitted_by', 'status'])]
class MahaprasadSlot extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'submitted_by');
    }
}
