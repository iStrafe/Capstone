<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'mobile_number',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
        ];
    }

    // Messages no admin has dealt with yet (the count in the admin sidebar).
    public function scopeUnhandled(Builder $query): void
    {
        $query->whereNull('handled_at');
    }
}
