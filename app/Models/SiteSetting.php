<?php

namespace App\Models;

use App\Models\Concerns\CleansUpFiles;
use App\Models\Concerns\FlushesPortfolioCache;
use App\Support\PortfolioCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Singleton holding website-wide branding, SEO and contact settings.
 */
#[Fillable([
    'site_name', 'tagline', 'logo', 'favicon', 'primary_color', 'accent_color',
    'meta_title', 'meta_description', 'og_image', 'footer_text',
    'contact_notification_email', 'show_contact_form', 'default_theme',
])]
class SiteSetting extends Model
{
    use CleansUpFiles, FlushesPortfolioCache;

    /**
     * Mirrors the database defaults so freshly created settings are complete.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'primary_color' => '#4F46E5',
        'accent_color' => '#14B8A6',
        'default_theme' => 'light',
        'show_contact_form' => true,
    ];

    public static function current(): self
    {
        return PortfolioCache::remember('settings', fn (): self => self::query()->firstOrCreate([], [
            'site_name' => config('app.name'),
        ]));
    }

    public function fileColumns(): array
    {
        return ['logo', 'favicon', 'og_image'];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'show_contact_form' => 'boolean',
        ];
    }
}
