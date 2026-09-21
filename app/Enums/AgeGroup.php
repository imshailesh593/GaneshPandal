<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AgeGroup: string implements HasLabel
{
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';

    public function getLabel(): string
    {
        return match ($this) {
            self::Small => 'Lahan gat (लहान गट)',
            self::Medium => 'Madhyam gat (मध्यम गट)',
            self::Large => 'Motha gat (मोठा गट)',
        };
    }

    public function marathi(): string
    {
        return match ($this) {
            self::Small => 'लहान गट',
            self::Medium => 'मध्यम गट',
            self::Large => 'मोठा गट',
        };
    }

    /** Marathi wording for a gender within this group: children are मुले/मुली, adults पुरुष/महिला. */
    public function genderLabel(Gender $gender): string
    {
        $adult = $this === self::Large;

        return match ($gender) {
            Gender::Male => $adult ? 'पुरुष' : 'मुले',
            Gender::Female => $adult ? 'महिला' : 'मुली',
        };
    }
}
