<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'telephone_number',
        'mobile_number',
        'cat_name',
        'cat_image',
        'age',
        'color',
        'breed',
        'sex',
        'date_of_adoption',
    ];
}
