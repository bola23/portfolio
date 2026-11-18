<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Read-through cache for public portfolio content.
 *
 * Every key is namespaced with a version token. Any content change rotates
 * the token, invalidating all public content at once without needing cache
 * tags (works with the file, database and redis stores alike). Stale entries
 * simply expire after their TTL.
 */
final class PortfolioCache
{
    private const VERSION_KEY = 'portfolio:cache-version';

    private const TTL_HOURS = 24;

    /**
     * @template TValue
     *
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    public static function remember(string $key, Closure $callback): mixed
    {
        return Cache::remember(self::key($key), now()->addHours(self::TTL_HOURS), $callback);
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, Str::random(12));
    }

    private static function key(string $key): string
    {
        $version = Cache::rememberForever(self::VERSION_KEY, fn (): string => Str::random(12));

        return "portfolio:{$version}:{$key}";
    }
}
