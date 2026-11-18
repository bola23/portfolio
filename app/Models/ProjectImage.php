<?php

namespace App\Models;

use App\Models\Concerns\CleansUpFiles;
use App\Models\Concerns\FlushesPortfolioCache;
use Database\Factories\ProjectImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'path', 'caption', 'sort_order'])]
class ProjectImage extends Model
{
    /** @use HasFactory<ProjectImageFactory> */
    use CleansUpFiles, FlushesPortfolioCache, HasFactory;

    public function fileColumns(): array
    {
        return ['path'];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
