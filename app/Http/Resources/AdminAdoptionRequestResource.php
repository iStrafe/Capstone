<?php

namespace App\Http\Resources;

use App\Models\AdoptionRequest;
use App\Models\Cat;
use Illuminate\Http\Request;

/**
 * An adoption request in the admin list: the applicant's own fields plus where to review it and
 * how busy its cat is. Load the cat with withExists(... as is_reserved) and withPendingRequestCount().
 *
 * @mixin AdoptionRequest
 */
class AdminAdoptionRequestResource extends AdoptionRequestResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'url' => route('admin.requests.show', $this->resource),
            'catPendingCount' => $this->whenLoaded('cat', fn () => $this->cat?->getAttribute('pending_requests_count')),
            'catPendingLimit' => Cat::MAX_PENDING_REQUESTS,
            'catArchived' => $this->whenLoaded('cat', fn () => $this->cat?->archived_at !== null),
        ];
    }
}
