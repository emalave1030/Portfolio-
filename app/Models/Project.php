<?php

namespace App\Models;

use App\Models\Concerns\ClearsPortfolioCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use ClearsPortfolioCache;

    protected $fillable = [
        'title',
        'description',
        'stack',
        'link',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'stack' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
