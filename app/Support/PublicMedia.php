<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Cat photos, cat clips and news images. They live on the media disk (storage/app/public by
 * default, or S3 by setting MEDIA_DISK), never in public/ or git. The database keeps only the
 * file name, so moving to another disk is a config change.
 */
class PublicMedia
{
    public const DIRECTORY = 'images';

    public static function disk(): Filesystem
    {
        return Storage::disk(self::diskName());
    }

    public static function diskName(): string
    {
        return config('filesystems.media_disk', 'public');
    }

    /** Names are file names, or a path inside images/ for a few older photos (cats/1727080976.jpg). */
    public static function path(string $name): string
    {
        $name = ltrim(str_replace('\\', '/', $name), '/');

        return self::DIRECTORY.'/'.(str_contains($name, '..') ? basename($name) : $name);
    }

    public static function url(?string $name): ?string
    {
        if (! $name) {
            return null;
        }

        $config = config('filesystems.disks.'.self::diskName());

        // A local disk is served through the public/storage link. Build the link from the current
        // host rather than APP_URL, so `php artisan serve` on any port still shows the photos.
        if (($config['driver'] ?? null) === 'local') {
            $prefix = trim((string) parse_url((string) ($config['url'] ?? '/storage'), PHP_URL_PATH), '/');

            return asset(ltrim($prefix.'/'.self::path($name), '/'));
        }

        return self::disk()->url(self::path($name));
    }

    public static function exists(?string $name): bool
    {
        return $name !== null && $name !== '' && self::disk()->exists(self::path($name));
    }

    /** Store an upload under a random, server-chosen name and extension and return that name. */
    public static function store(UploadedFile $file): string
    {
        $name = $file->hashName();
        self::disk()->putFileAs(self::DIRECTORY, $file, $name, ['visibility' => 'public']);

        return $name;
    }

    public static function delete(string $name): void
    {
        self::disk()->delete(self::path($name));
    }
}
