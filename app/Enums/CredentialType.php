<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CredentialType: string implements HasLabel
{
    case Education = 'education';
    case Certification = 'certification';

    public function getLabel(): string
    {
        return match ($this) {
            self::Education => 'Education',
            self::Certification => 'Certification / Course',
        };
    }
}
