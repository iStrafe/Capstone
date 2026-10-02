<?php

namespace App\Http\Resources;

use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A cat as the admin pages see it: the details the editor needs, where it stands on the
 * public site, and the admin URLs for it.
 *
 * Load with withExists(... as is_adopted / is_reserved) and withPendingRequestCount() to
 * avoid a query per cat for the state and the request count.
 *
 * @mixin Cat
 */
class AdminCatResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->cat_name,
            'age' => $this->age,
            'ageLabel' => CatResource::ageLabel($this->age),
            'sex' => $this->sex,
            'color' => $this->color,
            'breed' => $this->breed,
            'medicalRecord' => $this->Medical_Record,
            'status' => $this->status,
            // For StatusBadge: available, reserved, adopted, inactive or archived.
            'state' => $this->state(),
            'pendingCount' => (int) ($this->pending_requests_count ?? 0),
            'image' => $this->cat_image ? asset('images/'.$this->cat_image) : null,
            'placeholder' => asset('images/placeholder.png'),
            'clip' => $this->cat_clip ? asset('images/'.$this->cat_clip) : null,
            'updatedAt' => $this->updated_at?->format('M j, Y'),
            'archivedAt' => $this->archived_at?->format('M j, Y'),
            'archiveReason' => $this->archive_reason,
            'publicUrl' => route('cats.show', $this->resource),
            'editUrl' => route('admin.cats.edit', $this->resource),
            'updateUrl' => route('admin.cats.update', $this->resource),
            'archiveUrl' => route('admin.cats.archive', $this->resource),
            'restoreUrl' => route('admin.cats.restore', $this->resource),
            'deleteUrl' => route('admin.cats.destroy', $this->resource),
        ];
    }

    private function state(): string
    {
        return match (true) {
            $this->archived_at !== null => 'archived',
            (bool) $this->getAttribute('is_adopted') => 'adopted',
            (bool) $this->getAttribute('is_reserved') => 'reserved',
            $this->status !== Cat::STATUS_ACTIVE => 'inactive',
            default => 'available',
        };
    }
}
