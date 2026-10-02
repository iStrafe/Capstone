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
            'description' => ['required', 'string'],
            'event_date' => ['required', 'date'],
            'eventimage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            // Editing only: drop the current image without uploading another.
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // PHP drops a file over its upload_max_filesize before Laravel sees it.
            'eventimage.uploaded' => 'The image didn’t upload. This server takes files up to '.UploadLimit::label(UploadLimit::perFile()).', set by upload_max_filesize in php.ini.',
            'eventimage.max' => 'The image can be up to 10 MB.',
        ];
    }
}
