<?php

namespace App\Support;

use App\Models\Cat;
use Illuminate\Http\Request;

/**
 * The cat pictured beside the log in and register forms: the one the visitor was about to
 * adopt when they were asked to log in, otherwise a random available cat with a photo.
 */
class AuthPanelCat
{
    /**
     * @return array{name: string, image: ?string, placeholder: string, adopting: bool}|null
     */
    public static function for(Request $request): ?array
    {
        $wanted = self::wantedCat($request);
        $cat = $wanted ?? self::randomCatWithPhoto();

        if (! $cat) {
            return null;
        }

        return [
            'name' => $cat->cat_name,
            'image' => self::photoExists($cat) ? PublicMedia::url($cat->cat_image) : null,
            'placeholder' => asset('images/placeholder.png'),
            'adopting' => $wanted !== null,
        ];
    }

    /** The cat in the adoption page URL saved by the auth middleware, e.g. /cat/4/adopt. */
    private static function wantedCat(Request $request): ?Cat
    {
        $path = (string) parse_url((string) $request->session()->get('url.intended', ''), PHP_URL_PATH);

        if (! preg_match('#/cat/(\d+)/adopt$#', $path, $match)) {
            return null;
        }

        return Cat::available()->find((int) $match[1]);
    }

    private static function randomCatWithPhoto(): ?Cat
    {
        return Cat::available()
            ->whereNotNull('cat_image')
            ->inRandomOrder()
            ->limit(5)
            ->get()
            ->first(fn (Cat $cat) => self::photoExists($cat));
    }

    private static function photoExists(Cat $cat): bool
    {
        return PublicMedia::exists($cat->cat_image);
    }
}
