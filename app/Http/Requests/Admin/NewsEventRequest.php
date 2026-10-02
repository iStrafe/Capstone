<?php

namespace App\Http\Requests\Admin;

use App\Support\UploadLimit;
use Illuminate\Foundation\Http\FormRequest;

class NewsEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            // The date input sends Y-m-d; other formats let "99999-01-01" slip through as 2009.
            'event_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:2000-01-01', 'before_or_equal:2100-12-31'],
            'eventimage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            // Editing only: drop the current image without uploading another.
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // PHP drops a failed upload before Laravel sees it; say why.
            'eventimage.uploaded' => UploadLimit::failure($this->file('eventimage'), 'image'),
            'eventimage.max' => 'The image can be up to 10 MB.',
            'event_date.date_format' => 'Enter a date like 2026-10-31.',
            'event_date.after_or_equal' => 'Enter a date in 2000 or later.',
            'event_date.before_or_equal' => 'Enter a date before 2101.',
        ];
    }

    public function attributes(): array
    {
        return ['event_date' => 'event date', 'eventimage' => 'image'];
    }
}
