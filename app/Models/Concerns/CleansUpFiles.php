<?php

namespace App\Models\Concerns;

use App\Services\ImageOptimizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Removes files from the public disk when a file column is replaced
 * or when the record is permanently deleted.
 *
 * @mixin Model
 */
trait CleansUpFiles
{
    /**
     * @return list<string>
     */
    abstract public function fileColumns(): array;

    public static function bootCleansUpFiles(): void
    {
        static::updated(function (Model $model): void {
            foreach ($model->fileColumns() as $column) {
                $original = $model->getOriginal($column);

                if ($model->wasChanged($column) && filled($original)) {
                    app(ImageOptimizer::class)->delete($original);
                }
            }
        });

        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive(static::class), true);

        static::{$usesSoftDeletes ? 'forceDeleted' : 'deleted'}(function (Model $model): void {
            foreach ($model->fileColumns() as $column) {
                app(ImageOptimizer::class)->delete($model->{$column});
            }
        });
    }
}
