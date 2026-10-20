<?php

namespace App\Models;

use App\Models\Concerns\ClearsPortfolioCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use ClearsPortfolioCache;

    protected $fillable = [
        'name',
        'group',
        'is_highlighted',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_highlighted' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
