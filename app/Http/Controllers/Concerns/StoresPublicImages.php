<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Support\PublicMedia;
use Illuminate\Http\UploadedFile;

trait StoresPublicImages
{
    /**
     * Store an upload on the media disk under a random, server-chosen name and extension,
     * so uploads can't overwrite each other and the client's filename is never used.
     */
    protected function moveToPublicImages(UploadedFile $file): string
    {
        return PublicMedia::store($file);
    }

    /**
     * Delete uploads that were replaced or removed, so they stop being served. Only files this
     * site stored itself (random 40-character names) and that nothing else still uses; the older
     * images that came with the site may be shared between records.
     */
    protected function deleteUnusedPublicImages(?string ...$names): void
    {
        foreach (array_filter($names) as $name) {
            if (! preg_match('/^[A-Za-z0-9]{40}\.[A-Za-z0-9]+$/', $name)) {
                continue;
            }

            $used = Cat::where('cat_image', $name)->orWhere('cat_clip', $name)->exists()
                || NewsEvent::where('eventimage', $name)->exists();

            if (! $used) {
                PublicMedia::delete($name);
            }
        }
    }
}
