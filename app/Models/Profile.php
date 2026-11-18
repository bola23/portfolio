<?php

namespace App\Models;

use App\Enums\AvailabilityStatus;
use App\Models\Concerns\CleansUpFiles;
use App\Models\Concerns\FlushesPortfolioCache;
use App\Support\PortfolioCache;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Singleton holding the portfolio owner's professional profile.
 */
#[Fillable([
    'name', 'headline', 'specialization', 'short_intro', 'bio', 'approach', 'expertise_areas',
    'years_of_experience', 'availability_status', 'availability_note', 'avatar', 'cv_path',
    'email', 'phone', 'show_phone', 'location',
])]
class Profile extends Model
{
    use CleansUpFiles, FlushesPortfolioCache;

    /**
     * Mirrors the database defaults so a freshly created profile is complete.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'years_of_experience' => 0,
        'availability_status' => 'open',
        'show_phone' => false,
    ];

    public static function current(): self
    {
        return PortfolioCache::remember('profile', fn (): self => self::query()->firstOrCreate([], [
            'name' => config('app.name'),
            'headline' => 'Developer',
        ]));
    }

    public function fileColumns(): array
    {
        return ['avatar', 'cv_path'];
    }

    public function firstName(): string
    {
        return str($this->name)->before(' ')->toString();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expertise_areas' => 'array',
            'years_of_experience' => 'integer',
            'availability_status' => AvailabilityStatus::class,
            'show_phone' => 'boolean',
        ];
    }
}
