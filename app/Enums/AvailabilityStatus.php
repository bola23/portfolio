<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AvailabilityStatus: string implements HasColor, HasLabel
{
    case Open = 'open';
    case Freelance = 'freelance';
    case Limited = 'limited';
    case Unavailable = 'unavailable';

    public function getLabel(): string
    {
        return match ($this) {
            self::Open => 'Open to new opportunities',
            self::Freelance => 'Available for freelance work',
            self::Limited => 'Limited availability',
            self::Unavailable => 'Not available right now',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open, self::Freelance => 'success',
            self::Limited => 'warning',
            self::Unavailable => 'gray',
        };
    }

    public function isAvailable(): bool
    {
        return $this !== self::Unavailable;
    }
}
