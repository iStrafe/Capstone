<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cat extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_name',
        'cat_image',
        'cat_clip',
        'age',
        'color',
        'breed',
        'sex',
        'status',
        'Medical_Record',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * Cats shown to the public and open for adoption requests.
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->whereNull('archived_at');
    }

    public function adoptionRequests(): HasMany
    {
        return $this->hasMany(AdoptionRequest::class);
    }
}
