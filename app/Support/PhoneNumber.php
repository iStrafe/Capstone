<?php

namespace App\Support;

/**
 * Phone numbers typed into the contact and adoption forms. Spaces, dashes, dots and brackets
 * are dropped, then what's left must be 7 to 15 digits with an optional leading +.
 */
class PhoneNumber
{
    public const RULE = 'regex:/^\+?[0-9]{7,15}$/';

    public const MESSAGE = 'Enter a phone number using digits, like 0917 123 4567.';

    public static function normalize(mixed $value): mixed
    {
        return is_string($value) ? preg_replace('/[\s\-.()]/', '', $value) : $value;
    }
}
