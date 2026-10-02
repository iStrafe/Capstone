<?php

namespace App\Http\Requests;

use App\Models\Cat;
use App\Support\PhoneNumber;
use App\Support\UploadLimit;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdoptionRequest extends FormRequest
{
    /** How far ahead a pickup day can be booked. */
    public const PICKUP_MONTHS_AHEAD = 3;

    public static function latestPickup(): string
    {
        return today()->addMonths(self::PICKUP_MONTHS_AHEAD)->toDateString();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => PhoneNumber::normalize($this->input('phone'))]);
        }
    }

    public function rules(): array
    {
        return [
            // The cat's details come from its record, so only the cat's id is taken from the form.
            'cat_id' => ['bail', 'required', 'integer', $this->catAcceptsRequest(...)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', PhoneNumber::RULE],
            // The day the applicant wants to take the cat home, so it can't be in the past.
            'date_of_adoption' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.self::latestPickup()],
            // The first adoption term is "Provide a valid ID", so at least one photo is needed.
            'valid_id' => ['required', 'array', 'min:1', 'max:2'],
            'valid_id.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
            // The applicant has read the adoption terms on the request page and agrees to them.
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'cat_id.required' => 'Choose a cat to adopt from the list.',
            'cat_id.integer' => 'That cat is no longer available for adoption.',
            'date_of_adoption.after_or_equal' => 'Choose today or a later date for the adoption.',
            'date_of_adoption.before_or_equal' => 'Choose a day within the next '.self::PICKUP_MONTHS_AHEAD.' months.',
            'phone.regex' => PhoneNumber::MESSAGE,
            // PHP drops a failed upload before Laravel sees it; say why.
            'valid_id.*.uploaded' => UploadLimit::failure($this->file('valid_id.0') ?? $this->file('valid_id.1'), 'ID photo'),
            'valid_id.required' => 'Add a photo of at least one valid ID.',
            'valid_id.min' => 'Add a photo of at least one valid ID.',
            'valid_id.max' => 'You can add up to 2 ID photos.',
            'valid_id.*.image' => 'Each ID must be a JPG or PNG photo.',
            'valid_id.*.mimes' => 'Each ID must be a JPG or PNG photo.',
            'valid_id.*.max' => 'Each ID photo must be 2 MB or smaller.',
            'terms.accepted' => 'Please read the adoption terms and tick the box to agree.',
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
