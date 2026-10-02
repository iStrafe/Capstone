<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CatRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cat_name' => ['required', 'string', 'max:255'],
            'cat_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'cat_clip' => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,flv', 'max:25600'],
            // Whole years; leave empty when unknown.
            'age' => ['nullable', 'integer', 'min:0', 'max:30'],
            'color' => ['nullable', 'string', 'max:50'],
            'breed' => ['nullable', 'string', 'max:100'],
            'sex' => ['required', 'in:Male,Female'],
            'Medical_Record' => ['nullable', 'string', 'max:255'],
            // Status is only chosen when editing; new cats start Active, and archived cats stay
            // archived until they're restored.
            'status' => [$this->isMethod('post') || $this->route('cat')?->archived_at ? 'nullable' : 'required', 'in:Active,Inactive'],
        ];
    }
}
