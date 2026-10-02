<?php

namespace App\Http\Resources;

use App\Models\NewsEvent;
use Illuminate\Http\Request;

/**
 * A news post or event for the admin list and editor: the public fields plus the admin URLs.
 *
 * @mixin NewsEvent
 */
class AdminNewsEventResource extends NewsEventResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'editUrl' => route('news-events.edit', $this->id),
            'updateUrl' => route('news-events.update', $this->id),
            'deleteUrl' => route('news-events.destroy', $this->id),
        ];
    }
}
