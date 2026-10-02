<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Text taken from the query string, such as a search box. A tampered value like ?q[]=x
 * arrives as an array, which counts as empty instead of crashing.
 */
class QueryText
{
    public static function get(Request $request, string $key, int $limit = 255): string
    {
        $value = $request->query($key);

        return is_string($value) ? Str::limit(trim($value), $limit, '') : '';
    }

    /**
     * A LIKE pattern that finds the text anywhere, with % and _ matched literally. Use with ESCAPE '!'.
     * Not a backslash: PHP 8.3's PDO reads '\' as an unfinished string and loses the placeholders after it.
     */
    public static function like(string $text): string
    {
        return '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], Str::lower($text)).'%';
    }
}
