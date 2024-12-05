<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'Medical_Record',
       
    ];
}
