<?php

namespace App\Models;

use App\Models\Concerns\ClearsPortfolioCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use ClearsPortfolioCache;

    protected $fillable = [
        'role',
        'company',
        'location',
        'start_date',
        'end_date',
        'bullets',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'bullets' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The only place a date is turned into text. "Aug 2022 – Present".
     */
    protected function dateRange(): Attribute
    {
        return Attribute::get(fn (): string => sprintf(
            '%s – %s',
            $this->start_date->format('M Y'),
            $this->end_date?->format('M Y') ?? 'Present',
        ));
    }

    protected function isCurrent(): Attribute
    {
        return Attribute::get(fn (): bool => $this->end_date === null);
    }
}
