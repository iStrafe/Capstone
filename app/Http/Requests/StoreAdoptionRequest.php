<?php

namespace App\Http\Requests;

use App\Models\Cat;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdoptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // The cat's details come from its record, so only the cat's id is taken from the form.
            'cat_id' => ['bail', 'required', 'integer', $this->catAcceptsRequest(...)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            // The day the applicant wants to take the cat home, so it can't be in the past.
            'date_of_adoption' => ['required', 'date', 'after_or_equal:today'],
            'valid_id' => ['nullable', 'array', 'max:2'],
            'valid_id.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'cat_id.required' => 'Choose a cat to adopt from the list.',
            'cat_id.integer' => 'That cat is no longer available for adoption.',
            'date_of_adoption.after_or_equal' => 'Choose today or a later date for the adoption.',
        ];
    }

    // Same rules as the adoption list (Cat::available), plus one open request per applicant and a cap on pending requests.
    private function catAcceptsRequest(string $attribute, mixed $value, Closure $fail): void
    {
        $cat = Cat::find($value);
        $refusal = $cat ? $cat->requestRefusalFor($this->user()) : 'That cat is no longer available for adoption.';

        if ($refusal !== null) {
            $fail($refusal);
        }
    }
}
