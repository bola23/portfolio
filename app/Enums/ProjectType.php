<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectType: string implements HasLabel
{
    case WebApp = 'web_app';
    case WebAndMobile = 'web_mobile';
    case Api = 'api';
    case Dashboard = 'dashboard';
    case Website = 'website';
    case OpenSource = 'open_source';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::WebApp => 'Web Application',
            self::WebAndMobile => 'Web & Mobile Platform',
            self::Api => 'API / Backend Service',
            self::Dashboard => 'Admin Dashboard',
            self::Website => 'Website',
            self::OpenSource => 'Open Source',
            self::Other => 'Other',
        };
    }
}
