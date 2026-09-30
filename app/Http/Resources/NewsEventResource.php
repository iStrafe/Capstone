<?php

namespace App\Http\Resources;

use App\Models\NewsEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A news item or event as the public React pages see it (Home's events strip, /events).
 *
 * @mixin NewsEvent
 */
class NewsEventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            // For display, e.g. "Oct 9, 2026".
            'date' => $this->event_date?->format('M j, Y'),
            // For <time datetime> and sorting.
            'isoDate' => $this->event_date?->format('Y-m-d'),
            'image' => $this->eventimage ? asset('images/'.$this->eventimage) : null,
            // Today counts as upcoming.
            'isUpcoming' => $this->event_date !== null && $this->event_date->gte(today()),
        ];
    }
}
