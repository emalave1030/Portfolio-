<?php

namespace App\Models;

use App\Models\Concerns\ClearsPortfolioCache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    use ClearsPortfolioCache;

    protected $fillable = [
        'name',
        'title',
        'location',
        'email',
        'site',
        'github_url',
        'linkedin_url',
        'languages',
        'about',
        'stats',
    ];

    protected function casts(): array
    {
        return [
            'about' => 'array',
            'stats' => 'array',
        ];
    }

    /**
     * The hero prints the surname in the accent color, so the split happens here
     * rather than in the template.
     */
    protected function givenName(): Attribute
    {
        return Attribute::get(fn (): string => Str::beforeLast($this->name, ' '));
    }

    protected function familyName(): Attribute
    {
        return Attribute::get(fn (): string => Str::contains($this->name, ' ')
            ? Str::afterLast($this->name, ' ')
            : '');
    }
}
