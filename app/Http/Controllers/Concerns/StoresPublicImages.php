<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;

trait StoresPublicImages
{
    /**
     * Move an upload into public/images under a random, server-chosen name and extension,
     * so uploads can't overwrite each other and the client's filename never reaches the web root.
     */
    protected function moveToPublicImages(UploadedFile $file): string
    {
        $name = $file->hashName();
        $file->move(public_path('images'), $name);

        return $name;
    }
}
