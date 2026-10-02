<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

/**
 * Removes NUL characters from form input. Postgres can't store them in text, and the driver
 * would otherwise cut the text short at the first one without saying so.
 */
class StripNullBytes extends TransformsRequest
{
    protected function transform($key, $value)
    {
        return is_string($value) ? str_replace("\0", '', $value) : $value;
    }
}
