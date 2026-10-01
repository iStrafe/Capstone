<?php

namespace App\Http\Resources;

use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A cat as the public React pages see it.
 *
 * @mixin Cat
 */
class CatResource extends JsonResource
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
            'ageLabel' => self::ageLabel($this->age),
            'sex' => $this->sex,
            'color' => $this->color,
            'breed' => $this->breed,
            // Null when there is no photo; the page then shows the generic placeholder.
            'image' => $this->cat_image ? asset('images/'.$this->cat_image) : null,
            'placeholder' => asset('images/placeholder.png'),
            'clip' => $this->cat_clip ? asset('images/'.$this->cat_clip) : null,
            'url' => route('cats.show', $this->resource),
            // The adoption request page; guests are sent to log in first and come back here.
            'adoptUrl' => route('adoption.start', $this->resource),
        ];
    }

    /** Ages are stored as whole years. */
    public static function ageLabel(?int $age): string
    {
        return match (true) {
            $age === null => 'Age unknown',
            $age < 1 => 'Under 1 year',
            $age === 1 => '1 year',
            default => $age.' years',
        };
    }
}
