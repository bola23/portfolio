<?php

namespace App\Models\Concerns;

use App\Support\PortfolioCache;

/**
 * Invalidates the public content cache whenever the model changes,
 * so admin edits are reflected on the website immediately.
 */
trait FlushesPortfolioCache
{
    public static function bootFlushesPortfolioCache(): void
    {
        static::saved(fn () => PortfolioCache::flush());
        static::deleted(fn () => PortfolioCache::flush());

        if (method_exists(static::class, 'restored')) {
            static::restored(fn () => PortfolioCache::flush());
        }
    }
}
