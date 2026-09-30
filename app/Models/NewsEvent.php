<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsEvent extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'event_date', 'eventimage'];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }
}
