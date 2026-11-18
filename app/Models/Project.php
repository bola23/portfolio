<?php

namespace App\Models;

use App\Enums\ProjectType;
use App\Models\Concerns\CleansUpFiles;
use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id', 'title', 'slug', 'excerpt', 'type', 'region', 'role',
    'overview', 'problem', 'solution', 'responsibilities', 'architecture', 'features',
    'thumbnail', 'live_url', 'repo_url', 'is_featured', 'is_published', 'published_at',
    'sort_order', 'meta_title', 'meta_description',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use CleansUpFiles, FlushesPortfolioCache, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (Project $project): void {
            if ($project->is_published && $project->published_at === null) {
                $project->published_at = now();
            }
        });

        static::forceDeleting(function (Project $project): void {
            $project->images()->get()->each->delete();
        });
    }

    public function fileColumns(): array
    {
        return ['thumbnail'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @return HasMany<ProjectImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ProjectType::class,
            'features' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }
}
