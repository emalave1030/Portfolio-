<?php

namespace App\Support;

/**
 * The single cache entry backing GET /api/portfolio.
 *
 * The payload is written with Cache::rememberForever and invalidated by
 * App\Models\Concerns\ClearsPortfolioCache, so the key lives in one place
 * that both the reader and the writers can point at.
 */
final class PortfolioCache
{
    public const KEY = 'portfolio.payload';
}
