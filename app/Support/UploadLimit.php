<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * PHP's own upload limits from php.ini. A file over upload_max_filesize arrives as a failed
 * upload, and a form over post_max_size is refused before Laravel sees it (413), so the admin
 * editors check against these before sending.
 */
class UploadLimit
{
    /** The biggest single file PHP accepts, in bytes. */
    public static function perFile(): int
    {
        return min(self::bytes((string) ini_get('upload_max_filesize')), self::total());
    }

    /** The biggest whole form PHP accepts, in bytes. */
    public static function total(): int
    {
        return self::bytes((string) ini_get('post_max_size'));
    }

    /** A size like "2 MB" for messages. */
    public static function label(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return rtrim(rtrim(number_format($bytes / 1024 / 1024, 1, '.', ''), '0'), '.').' MB';
        }

        return max(1, (int) round($bytes / 1024)).' KB';
    }

    /** Why PHP dropped an upload, from its error code, for the "uploaded" validation message. */
    public static function failure(mixed $file, string $noun): string
    {
        $error = $file instanceof UploadedFile ? $file->getError() : UPLOAD_ERR_INI_SIZE;

        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "The {$noun} didn’t upload. This server takes files up to ".self::label(self::perFile()).', set by upload_max_filesize in php.ini.',
            UPLOAD_ERR_PARTIAL => "The {$noun} only partly uploaded. Please try again.",
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE => "The {$noun} didn’t upload because PHP couldn’t save it in its temporary folder. Set upload_tmp_dir in php.ini to a folder PHP can write to.",
            default => "The {$noun} didn’t upload (PHP upload error {$error}). Please try again.",
        };
    }

    /** For the editors in the admin pages. */
    public static function toArray(): array
    {
        return ['file' => self::perFile(), 'total' => self::total()];
    }

    /** Turns php.ini shorthand such as "2M" or "1G" into bytes. 0 or empty means no limit. */
    public static function bytes(string $value): int
    {
        $value = trim($value);
        $number = (int) $value;

        if ($number <= 0) {
            return PHP_INT_MAX;
        }

        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
