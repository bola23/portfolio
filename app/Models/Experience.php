<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'company', 'company_url', 'position', 'employment_type', 'work_mode', 'location',
    'start_date', 'end_date', 'is_current', 'description', 'responsibilities', 'sort_order', 'is_visible',
])]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use FlushesPortfolioCache, HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Experience $experience): void {
            if ($experience->is_current) {
                $experience->end_date = null;
            }
        });
    }

    /**
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Human readable period, e.g. "Jan 2025 — Present".
     *
     * @return Attribute<string, never>
     */
    protected function period(): Attribute
    {
        return Attribute::get(fn (): string => sprintf(
            '%s — %s',
            $this->start_date->format('M Y'),
            $this->is_current || $this->end_date === null ? 'Present' : $this->end_date->format('M Y'),
        ));
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('is_visible', true);
    }

    /**
     * Current roles first, then most recent start date, then manual order.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('is_current')->orderByDesc('start_date');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_visible' => 'boolean',
            'responsibilities' => 'array',
        ];
    }
}
