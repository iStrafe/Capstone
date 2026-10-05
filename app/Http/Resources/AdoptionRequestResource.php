<?php

namespace App\Http\Resources;

use App\Models\AdoptionRequest;
use App\Support\PublicMedia;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * An adoption request for the applicant's "My requests" page and the admin request tables.
 *
 * Valid IDs are personal documents: only how many were uploaded is exposed here, never their
 * file names or paths. Load the cat with ->with('cat') to get the cat block.
 *
 * @mixin AdoptionRequest
 */
class AdoptionRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $validIds = $this->valid_id;

        return [
            'id' => $this->id,
            'status' => $this->status?->value,
            // Lowercase key for StatusBadge: pending, approved, rejected or released.
            'statusKey' => $this->status ? strtolower($this->status->value) : null,
            // The cat's name when the request was sent; the cat record may since have been renamed or deleted.
            'catName' => $this->name_of_cat,
            'cat' => $this->whenLoaded('cat', fn () => $this->cat ? [
                'id' => $this->cat->id,
                'name' => $this->cat->cat_name,
                'image' => PublicMedia::url($this->cat->cat_image),
                'placeholder' => asset('images/placeholder.png'),
                'url' => route('cats.show', $this->cat),
                // Another applicant was approved for this cat (or already took it home). Load with
                // withExists(... as is_reserved) to get it; My requests uses it to explain a long wait.
                'reserved' => (bool) $this->cat->getAttribute('is_reserved'),
            ] : null),
            'applicant' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->mobile_phone ?? $this->home_phone,
                'address' => $this->address,
            ],
            // The day the applicant wants to take the cat home.
            'pickupDate' => $this->date_of_adoption?->format('M j, Y'),
            'sentAt' => $this->created_at?->format('M j, Y'),
            'approvedAt' => $this->approval_date?->format('M j, Y'),
            'releasedAt' => $this->Release_date?->format('M j, Y'),
            'validIdCount' => is_array($validIds) ? count($validIds) : 0,
        ];
    }
}
