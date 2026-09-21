<?php

namespace App\Models;

use App\Enums\AgeGroup;
use App\Enums\Gender;
use App\Enums\Position;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['game_id', 'member_id', 'participant_name', 'age_group', 'gender', 'position'])]
class GameParticipant extends Model
{
    protected function casts(): array
    {
        return [
            'age_group' => AgeGroup::class,
            'gender' => Gender::class,
            'position' => Position::class,
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function displayName(): string
    {
        return $this->member?->name ?? $this->participant_name ?? 'Unknown';
    }

    /** e.g. "लहान गट (मुली)" */
    public function categoryLabel(): string
    {
        return $this->age_group->marathi().' ('.$this->age_group->genderLabel($this->gender).')';
    }
}
