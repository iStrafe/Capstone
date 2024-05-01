<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatInfo extends Model
{
    use HasFactory;

    protected $fillable = [
            'name',
            'gender',
            'breed',
            'eye_color',
            'fur_color',
            'description',
            'status'
    ];
}
