<?php

namespace App\Models\Concerns;

use App\Support\PortfolioCache;
use Illuminate\Support\Facades\Cache;

/**
 * Drops the cached portfolio payload whenever content changes.
 *
 * Every model that contributes to GET /api/portfolio uses this trait, so a
 * save from Filament (or tinker, or a seeder) is live on the next request
 * with no TTL to wait out and no deploy.
 */
trait ClearsPortfolioCache
{
    public static function bootClearsPortfolioCache(): void
    {
        static::saved(static fn () => Cache::forget(PortfolioCache::KEY));
        static::deleted(static fn () => Cache::forget(PortfolioCache::KEY));
    }
}
