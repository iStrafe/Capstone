<?php

namespace App\Support;

use App\Models\Cat;
use App\Models\NewsEvent;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Cat photos, cat clips and news images: the media shown on the public site. They live on the
 * media disk (storage/app/public in development, an S3 bucket in production; MEDIA_DISK picks
 * it), never in public/ or git, and never in the database.
 *
 * The database keeps only the object key, e.g. cats/12/images/<40 random chars>.jpg. Older rows
 * still hold a bare file name from before keys (1730882812.png, or cats/1727080976.jpg for a
 * few early photos); those live under images/ on the disk until `php artisan app:migrate-media`
 * gives them a key.
 */
class PublicMedia
{
    /** Where files from before object keys live on the media disk. */
    public const DIRECTORY = 'images';

    /** Keys this class hands out: <owner>/<record id>/<images|videos>/<random name>.<ext>. */
    private const KEY = '#^(cats|news)/[0-9]+/(images|videos)/[A-Za-z0-9]{40}\.[A-Za-z0-9]{1,10}$#';

    /** Uploads never change under a key (each upload gets a new random name), so caches may keep them. */
    private const CACHE_CONTROL = 'public, max-age=31536000, immutable';

    public static function disk(): Filesystem
    {
        return Storage::disk(self::diskName());
    }

    public static function diskName(): string
    {
        return config('filesystems.media_disk', 'public');
    }

    /** True for an object key; false for a file name from before keys. */
    public static function isKey(?string $value): bool
    {
        return $value !== null && preg_match(self::KEY, $value) === 1;
    }

    /** Where a stored value lives on the media disk. */
    public static function path(string $value): string
    {
        if (self::isKey($value)) {
            return $value;
        }

        $name = ltrim(str_replace('\\', '/', $value), '/');

        return self::DIRECTORY.'/'.(str_contains($name, '..') ? basename($name) : $name);
    }

    /** The key a file for $record gets: cats/{id}/images/{name}, cats/{id}/videos/{name} or news/{id}/images/{name}. */
    public static function key(Model $record, string $kind, string $name): string
    {
        $owner = match (true) {
            $record instanceof Cat => 'cats',
            $record instanceof NewsEvent => 'news',
            default => throw new InvalidArgumentException('No media folder for '.$record::class.'.'),
        };

        if (! in_array($kind, ['images', 'videos'], true) || ! $record->getKey()) {
            throw new InvalidArgumentException("Can't build a media key for kind {$kind} on an unsaved record.");
        }

        return "{$owner}/{$record->getKey()}/{$kind}/{$name}";
    }

    /**
     * The URL the browser loads the file from. A local disk is served through public/storage. A
     * private bucket gets a presigned URL that is the same for an hour at a time, so browsers
     * and the CDN can cache it; MEDIA_SIGNED_URLS=false uses the disk's plain URL instead, for a
     * bucket published through CloudFront or a bucket policy.
     */
    public static function url(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $config = config('filesystems.disks.'.self::diskName());

        // Build the link from the current host rather than APP_URL, so `php artisan serve` on any
        // port still shows the photos.
        if (($config['driver'] ?? null) === 'local') {
            $prefix = trim((string) parse_url((string) ($config['url'] ?? '/storage'), PHP_URL_PATH), '/');

            return asset(ltrim($prefix.'/'.self::path($value), '/'));
        }

        if (! config('filesystems.media_signed_urls', true)) {
            return self::disk()->url(self::path($value));
        }

        // S3 refuses presigned URLs that last longer than 7 days.
        $minutes = min(10080, max(61, (int) config('filesystems.media_url_minutes', 180)));
        $signedAt = now()->startOfHour();

        // Signing from the start of the hour keeps the URL identical all hour, and every URL
        // handed out stays valid for at least $minutes - 60 minutes.
        return self::disk()->temporaryUrl(self::path($value), $signedAt->copy()->addMinutes($minutes), [
            'start_time' => $signedAt->getTimestamp(),
        ]);
    }

    /**
     * Whether the file is on the media disk. False, not an error, when the disk can't be reached:
     * Laravel's exists() throws even with 'throw' => false, and the log in page asks this.
     */
    public static function exists(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        try {
            return self::disk()->exists(self::path($value));
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    /**
     * Store an upload for $record under a random, server-chosen name and extension and return
     * its key. The client's file name is never used.
     *
     * @throws RuntimeException when the disk refuses the file
     */
    public static function store(UploadedFile $file, Model $record, string $kind): string
    {
        $key = self::key($record, $kind, $file->hashName());

        $stored = self::disk()->putFileAs(dirname($key), $file, basename($key), self::writeOptions());

        // An S3 disk logs the cause itself ('report' => true).
        if ($stored === false) {
            throw new RuntimeException("Could not store {$key} on the media disk.");
        }

        return $key;
    }

    /**
     * Copy a stream to $key, as the migration command does for files from before keys.
     */
    public static function writeStream(string $key, $stream): bool
    {
        return self::disk()->writeStream($key, $stream, self::writeOptions());
    }

    public static function delete(string $value): void
    {
        self::disk()->delete(self::path($value));
    }

    /**
     * A local public disk needs world-readable files. A bucket gets Flysystem's default private
     * ACL, never public-read: new buckets refuse public ACLs (Object Ownership "bucket owner
     * enforced"), and access is decided by the bucket, not by each file.
     */
    private static function writeOptions(): array
    {
        if ((config('filesystems.disks.'.self::diskName().'.driver')) === 'local') {
            return ['visibility' => 'public'];
        }

        return ['CacheControl' => self::CACHE_CONTROL];
    }
}
