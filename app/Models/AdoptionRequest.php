<?php

namespace App\Models;

use App\Enums\AdoptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionRequest extends Model
{
    protected $table = 'adoption_request';

    protected $fillable = [
        'cat_id',
        'user_id',
        'name',
        'address',
        'email',
        'home_phone',
        'mobile_phone',
        'valid_id',
        'name_of_cat',
        'breed',
        'approximate_age',
        'sex',
        'color',
        'date_of_adoption',
    ];

    protected function casts(): array
    {
        return [
            'status' => AdoptionStatus::class,
            'valid_id' => 'array',
            'date_of_adoption' => 'date',
            'approval_date' => 'datetime',
            'Release_date' => 'datetime',
        ];
    }

    public function cat(): BelongsTo
    {
        return $this->belongsTo(Cat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
