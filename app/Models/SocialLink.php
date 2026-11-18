<?php

namespace App\Models;

use App\Enums\SocialPlatform;
use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\SocialLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['platform', 'label', 'url', 'sort_order', 'is_visible', 'show_in_hero'])]
class SocialLink extends Model
{
    /** @use HasFactory<SocialLinkFactory> */
    use FlushesPortfolioCache, HasFactory;

    /**
     * @return Attribute<string, never>
     */
    protected function displayLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->label ?: $this->platform->getLabel());
    }

    /**
     * @return Attribute<string, never>
     */
    protected function href(): Attribute
    {
        return Attribute::get(fn (): string => $this->platform === SocialPlatform::Email && ! str_starts_with($this->url, 'mailto:')
            ? 'mailto:'.$this->url
            : $this->url);
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
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'platform' => SocialPlatform::class,
            'is_visible' => 'boolean',
            'show_in_hero' => 'boolean',
        ];
    }
}
