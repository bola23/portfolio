<?php

namespace App\Models;

use App\Enums\CredentialType;
use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\CredentialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'title', 'issuer', 'start_year', 'end_year', 'url', 'sort_order', 'is_visible'])]
class Credential extends Model
{
    /** @use HasFactory<CredentialFactory> */
    use FlushesPortfolioCache, HasFactory;

    /**
     * @return Attribute<string|null, never>
     */
    protected function period(): Attribute
    {
        return Attribute::get(fn (): ?string => match (true) {
            $this->start_year && $this->end_year && $this->start_year !== $this->end_year => "{$this->start_year} — {$this->end_year}",
            filled($this->end_year) => (string) $this->end_year,
            filled($this->start_year) => (string) $this->start_year,
            default => null,
        });
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
        $query->orderBy('sort_order')->orderByDesc('end_year');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => CredentialType::class,
            'start_year' => 'integer',
            'end_year' => 'integer',
            'is_visible' => 'boolean',
        ];
    }
}
