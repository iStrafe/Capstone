<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class adoption extends Model
{
    protected $fillable = [
        'name',
        'address',
        'home_phone',
        'mobile_phone',
        'valid_id',
        'name_of_cat',
        'breed',
        'approximate_age',
        'sex',
        'color',
        'date_of_adoption'
    ];
}
