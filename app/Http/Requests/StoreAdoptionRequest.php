<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdoptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // The cat's details come from its record, so only the cat's id is taken from the form.
            'cat_id' => ['required', 'integer', Rule::exists('cats', 'id')->whereNull('archived_at')],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'date_of_adoption' => ['required', 'date'],
            'valid_id' => ['nullable', 'array', 'max:2'],
            'valid_id.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'cat_id.required' => 'Choose a cat to adopt from the list.',
            'cat_id.exists' => 'That cat is no longer available for adoption.',
        ];
    }
}
