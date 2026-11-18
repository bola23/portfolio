<?php

namespace App\Enums;

use App\Support\Icon;
use Filament\Support\Contracts\HasLabel;

enum SocialPlatform: string implements HasLabel
{
    case GitHub = 'github';
    case LinkedIn = 'linkedin';
    case X = 'x';
    case GitLab = 'gitlab';
    case StackOverflow = 'stackoverflow';
    case Medium = 'medium';
    case YouTube = 'youtube';
    case WhatsApp = 'whatsapp';
    case Telegram = 'telegram';
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Website = 'website';
    case Email = 'email';

    public function getLabel(): string
    {
        return match ($this) {
            self::GitHub => 'GitHub',
            self::LinkedIn => 'LinkedIn',
            self::X => 'X (Twitter)',
            self::GitLab => 'GitLab',
            self::StackOverflow => 'Stack Overflow',
            self::Medium => 'Medium',
            self::YouTube => 'YouTube',
            self::WhatsApp => 'WhatsApp',
            self::Telegram => 'Telegram',
            self::Facebook => 'Facebook',
            self::Instagram => 'Instagram',
            self::Website => 'Website',
            self::Email => 'Email',
        };
    }

    /**
     * Icon key understood by {@see Icon}.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Website => 'ui-globe',
            self::Email => 'ui-mail',
            default => $this->value,
        };
    }
}
