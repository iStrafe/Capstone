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

    /**
     * Why an admin can't move this request to $status, or null when they can. Keeps one approved
     * adopter per cat, makes Released final, and stops decisions on archived cats.
     */
    public function statusChangeRefusal(AdoptionStatus $status): ?string
    {
        $from = $this->status ?? AdoptionStatus::Pending;
        $catName = $this->cat?->cat_name ?? $this->name_of_cat ?? 'this cat';

        if ($from === $status) {
            return 'This request is already '.$status->value.'.';
        }

        if (! in_array($status, $from->next(), true)) {
            return $from === AdoptionStatus::Released
                ? 'This cat already went home. A released request can\'t be changed.'
                : 'A '.strtolower($from->value).' request can\'t be marked '.strtolower($status->value).'.';
        }

        if ($this->cat === null || $status === AdoptionStatus::Rejected) {
            return null;
        }

        if ($this->cat->archived_at !== null && $status !== AdoptionStatus::Released) {
            return $catName.' is archived. Restore the cat before changing this request.';
        }

        $others = $this->cat->adoptionRequests()->whereKeyNot($this->getKey());

        if ($status === AdoptionStatus::Approved) {
            $holder = (clone $others)->whereIn('status', AdoptionStatus::reservingCat())->first();

            if ($holder) {
                return $holder->status === AdoptionStatus::Released
                    ? $catName.' already went home with '.$holder->name.'.'
                    : $catName.' already has an approved adopter ('.$holder->name.'). Reject that request first.';
            }
        }

        if ($status === AdoptionStatus::Pending) {
            if ((clone $others)->where('status', AdoptionStatus::Released)->exists()) {
                return $catName.' already went home with another adopter.';
            }

            if ($this->user_id !== null && (clone $others)->where('user_id', $this->user_id)->whereIn('status', AdoptionStatus::open())->exists()) {
                return 'This applicant already has another open request for '.$catName.'.';
            }
        }

        return null;
    }
}
