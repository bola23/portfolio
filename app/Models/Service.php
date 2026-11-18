<?php

namespace App\Models;

use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'slug', 'icon', 'summary', 'description', 'deliverables', 'sort_order', 'is_visible'])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use FlushesPortfolioCache, HasFactory;

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
        $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deliverables' => 'array',
            'is_visible' => 'boolean',
        ];
    }
}
