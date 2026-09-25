<?php

namespace App\Models;

use App\Enums\AdoptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cat extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'Active';

    public const STATUS_INACTIVE = 'Inactive';

    public const STATUS_ARCHIVED = 'ARCHIVED';

    /** A cat stops taking new requests once this many are waiting for a decision. */
    public const MAX_PENDING_REQUESTS = 10;

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
     * Cats shown to the public and open for adoption requests: not archived, Active, and not
     * already approved for or released to an applicant. Every public list and the adoption
     * form's validation use this scope, so they always agree.
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->notArchived()
            ->where('status', self::STATUS_ACTIVE)
            ->whereDoesntHave('adoptionRequests', fn (Builder $requests) => $requests->whereIn('status', AdoptionStatus::reservingCat()));
    }

    /**
     * The admin inventory: every cat that hasn't been archived, whatever its adoption state.
     */
    public function scopeNotArchived(Builder $query): void
    {
        $query->whereNull('archived_at');
    }

    /**
     * Adds pending_requests_count, used to tell when a cat has no room for more requests.
     */
    public function scopeWithPendingRequestCount(Builder $query): void
    {
        $query->withCount(['adoptionRequests as pending_requests_count' => fn (Builder $requests) => $requests->where('status', AdoptionStatus::Pending)]);
    }

    public function adoptionRequests(): HasMany
    {
        return $this->hasMany(AdoptionRequest::class);
    }

    public function isAvailable(): bool
    {
        return static::available()->whereKey($this->getKey())->exists();
    }

    public function hasRoomForRequests(): bool
    {
        $pending = $this->pending_requests_count
            ?? $this->adoptionRequests()->where('status', AdoptionStatus::Pending)->count();

        return $pending < self::MAX_PENDING_REQUESTS;
    }

    /**
     * Why the cat is off the adoption list: archived, adopted, reserved (an approved request
     * waiting for release) or inactive. Null while it is available.
     */
    public function unavailableReason(): ?string
    {
        return match (true) {
            $this->archived_at !== null => 'archived',
            $this->adoptionRequests()->where('status', AdoptionStatus::Released)->exists() => 'adopted',
            $this->adoptionRequests()->where('status', AdoptionStatus::Approved)->exists() => 'reserved',
            $this->status !== self::STATUS_ACTIVE => 'inactive',
            default => null,
        };
    }

    /**
     * Why $user can't send a new adoption request for this cat, or null when they can.
     */
    public function requestRefusalFor(User $user): ?string
    {
        $hasOpenRequest = $this->adoptionRequests()
            ->where('user_id', $user->getKey())
            ->whereIn('status', AdoptionStatus::open())
            ->exists();

        return match (true) {
            $hasOpenRequest => 'You already have an open adoption request for '.$this->cat_name.'. You can follow it on My Requests.',
            ! $this->isAvailable() => 'That cat is no longer available for adoption.',
            ! $this->hasRoomForRequests() => $this->cat_name.' already has '.self::MAX_PENDING_REQUESTS.' adoption requests waiting for a decision. Please choose another cat or try again later.',
            default => null,
        };
    }
}
