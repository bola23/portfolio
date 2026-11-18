<?php

namespace App\Models;

use App\Models\Concerns\CleansUpFiles;
use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['skill_category_id', 'name', 'icon', 'icon_path', 'color', 'proficiency', 'sort_order', 'is_visible'])]
class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use CleansUpFiles, FlushesPortfolioCache, HasFactory;

    public function fileColumns(): array
    {
        return ['icon_path'];
    }

    /**
     * @return BelongsTo<SkillCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
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
        $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'proficiency' => 'integer',
            'is_visible' => 'boolean',
        ];
    }
}
