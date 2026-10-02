<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NewsEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'eventimage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            // Editing only: drop the current image without uploading another.
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}
