<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

trait StoresPublicImages
{
    /**
     * Save $record together with its uploads. $uploads maps a column to [file or null, kind,
     * label], e.g. ['cat_image' => [$file, 'images', 'photo']]. A new record is inserted first,
     * since its key goes into the object keys (cats/{id}/images/...).
     *
     * The row and the files land together: when the disk refuses a file, or the database
     * refuses the row, nothing is saved and the files already stored are removed again, so no
     * row points at a missing file and no file is left without a row. Files the record pointed
     * at before are deleted only once the new ones are safely saved.
     *
     * @param  array<string, array{0: ?UploadedFile, 1: string, 2: string}>  $uploads
     * @param  array<int, ?string>  $alsoRemoved  files the record stopped using for other reasons
     */
    protected function saveWithMedia(Model $record, array $uploads, array $alsoRemoved = []): void
    {
        $previous = array_map(fn (string $column) => $record->getOriginal($column), array_combine(array_keys($uploads), array_keys($uploads)));
        $stored = [];
        $wasNew = ! $record->exists;

        try {
            DB::transaction(function () use ($record, $uploads, &$stored) {
                if (! $record->exists) {
                    $record->save();
                }

                foreach ($uploads as $column => [$file, $kind, $label]) {
                    if (! $file) {
                        continue;
                    }

                    try {
                        $stored[] = $record->{$column} = PublicMedia::store($file, $record, $kind);
                    } catch (Throwable $e) {
                        report($e);

                        throw ValidationException::withMessages([
                            $column => "The {$label} could not be saved. Please try again.",
                        ]);
                    }
                }

                $record->save();
            });
        } catch (Throwable $e) {
            foreach ($stored as $key) {
                PublicMedia::delete($key);
            }

            // The insert was rolled back; don't leave the model looking saved.
            if ($wasNew) {
                $record->exists = false;
                $record->setAttribute($record->getKeyName(), null);
            }

            throw $e;
        }

        $current = array_map(fn (string $column) => $record->{$column}, array_keys($uploads));
        $this->deleteUnusedPublicImages(...array_diff([...array_values($previous), ...$alsoRemoved], $current));
    }

    /**
     * Delete uploads that were replaced or removed, so they stop being served. Only files this
     * site stored itself (object keys, or the random 40-character names from before keys) and
     * that nothing else still uses; the older images that came with the site may be shared
     * between records.
     */
    protected function deleteUnusedPublicImages(?string ...$names): void
    {
        foreach (array_unique(array_filter($names)) as $name) {
            if (! PublicMedia::isKey($name) && ! preg_match('/^[A-Za-z0-9]{40}\.[A-Za-z0-9]+$/', $name)) {
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
