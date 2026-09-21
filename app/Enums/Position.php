<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Position: string implements HasLabel
{
    case First = 'first';
    case Second = 'second';
    case Third = 'third';

    public function getLabel(): string
    {
        return match ($this) {
            self::First => '1st place',
            self::Second => '2nd place',
            self::Third => '3rd place',
        };
    }

    /** Formal Marathi ordinal, used on certificates. */
    public function formal(): string
    {
        return match ($this) {
            self::First => 'प्रथम',
            self::Second => 'द्वितीय',
            self::Third => 'तृतीय',
        };
    }

    /** Everyday Marathi ordinal, used on the website. */
    public function casual(): string
    {
        return match ($this) {
            self::First => 'पहिला',
            self::Second => 'दुसरा',
            self::Third => 'तिसरा',
        };
    }

    public function number(): int
    {
        return match ($this) {
            self::First => 1,
            self::Second => 2,
            self::Third => 3,
        };
    }
}
