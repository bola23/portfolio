<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'icon', 'color', 'sort_order'])]
class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use FlushesPortfolioCache, HasFactory;

    /**
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * @return BelongsToMany<Experience, $this>
     */
    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(Experience::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }
}
