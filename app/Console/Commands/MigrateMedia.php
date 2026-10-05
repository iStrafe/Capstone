<?php

namespace App\Console\Commands;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Support\PublicMedia;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Gives every cat photo, cat clip and news image an object key (cats/{id}/images/..., etc.) on
 * the media disk, copying the file from where it is now. Run it after switching MEDIA_DISK to s3
 * with --from=public to upload the files from storage/app/public, or without --from to re-key
 * files that are already on the media disk.
 *
 * Nothing is deleted: the old files stay until `--prune=<manifest>` after the site has been
 * checked, and `--revert=<manifest>` points the records back at the old files.
 */
class MigrateMedia extends Command
{
    protected $signature = 'app:migrate-media
                            {--from= : Disk the files are on now (default: the media disk)}
                            {--dry-run : Only list what would be copied}
                            {--revert= : Point the records in this manifest back at their old files}
                            {--prune= : Delete the old files listed in this manifest, once their copies are verified}';

    protected $description = 'Copy cat and news media to object keys on the media disk and update the records, keeping the old files';

    /** Model, column and folder (images or videos) of every media column. */
    private const COLUMNS = [
        [Cat::class, 'cat_image', 'images'],
        [Cat::class, 'cat_clip', 'videos'],
        [NewsEvent::class, 'eventimage', 'images'],
    ];

    private const MANIFESTS = 'media-migrations';

    public function handle(): int
    {
        return match (true) {
            (bool) $this->option('revert') => $this->revert($this->manifest($this->option('revert'))),
            (bool) $this->option('prune') => $this->prune($this->manifest($this->option('prune'))),
            default => $this->migrate(),
        };
    }

    private function migrate(): int
    {
        $fromName = $this->option('from') ?: PublicMedia::diskName();
        $from = Storage::disk($fromName);
        $to = PublicMedia::disk();
        $dryRun = (bool) $this->option('dry-run');

        $counts = ['copied' => 0, 'done' => 0, 'missing' => 0, 'failed' => 0];
        $entries = [];

        foreach (self::COLUMNS as [$model, $column, $kind]) {
            /** @var Model $record */
            foreach ($model::query()->whereNotNull($column)->where($column, '!=', '')->lazyById() as $record) {
                $old = $record->getRawOriginal($column);
                $source = PublicMedia::path($old);

                // Already keyed and on the media disk: nothing to do.
                if (PublicMedia::isKey($old) && $to->exists($old)) {
                    $counts['done']++;

                    continue;
                }

                if (! $from->exists($source)) {
                    $counts['missing']++;
                    $this->warn(sprintf('Missing: %s #%d %s = %s (no %s on the %s disk). Left as it is.', class_basename($model), $record->getKey(), $column, $old, $source, $fromName));

                    continue;
                }

                $key = PublicMedia::isKey($old) ? $old : PublicMedia::key($record, $kind, Str::random(40).'.'.$this->extension($old));
                $this->line(sprintf('%s #%d %s: %s:%s -> %s:%s', class_basename($model), $record->getKey(), $column, $fromName, $source, PublicMedia::diskName(), $key), null, $dryRun ? 'normal' : 'v');

                if ($dryRun) {
                    $counts['copied']++;

                    continue;
                }

                if (! $this->copy($from, $source, $key)) {
                    $counts['failed']++;

                    continue;
                }

                // Only if an admin hasn't changed the record meanwhile; then the copy isn't needed.
                $updated = $record->newQuery()->toBase()->where('id', $record->getKey())->where($column, $old)->update([$column => $key]);

                if ($updated === 0 && $key !== $old) {
                    $to->delete($key);
                    $counts['failed']++;
                    $this->warn(sprintf('%s #%d changed while copying; run the command again.', class_basename($model), $record->getKey()));

                    continue;
                }

                $counts['copied']++;
                $entries[] = [
                    'table' => $record->getTable(), 'id' => $record->getKey(), 'column' => $column,
                    'old' => $old, 'new' => $key, 'from_disk' => $fromName, 'from_path' => $source,
                ];
            }
        }

        $this->table(['Result', 'Files'], [
            [$dryRun ? 'Would copy' : 'Copied and record updated', $counts['copied']],
            ['Already on the media disk under a key', $counts['done']],
            ['Missing (record left as it is)', $counts['missing']],
            ['Failed (record left as it is)', $counts['failed']],
        ]);

        if ($dryRun) {
            $this->info('Dry run: nothing was copied or changed.');

            return self::SUCCESS;
        }

        if ($entries !== []) {
            $path = self::MANIFESTS.'/'.now()->format('Ymd_His').'.json';
            $this->privateDisk()->put($path, json_encode([
                'media_disk' => PublicMedia::diskName(),
                'created_at' => now()->toIso8601String(),
                'entries' => $entries,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $this->info("Old files were kept. Manifest: {$path}");
            $this->line("Check the site, then delete the old files with: php artisan app:migrate-media --prune={$path}");
            $this->line("To undo: php artisan app:migrate-media --revert={$path}");
        }

        return $counts['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function copy(Filesystem $from, string $source, string $key): bool
    {
        $to = PublicMedia::disk();
        $size = $from->size($source);

        if (! $to->exists($key)) {
            $stream = $from->readStream($source);

            try {
                $written = $stream && PublicMedia::writeStream($key, $stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            if (! $written) {
                $this->error("Could not write {$key}.");

                return false;
            }
        }

        // Never point a record at a copy that isn't complete.
        if ($to->size($key) !== $size) {
            $to->delete($key);
            $this->error("The copy of {$source} at {$key} has the wrong size; removed it.");

            return false;
        }

        return true;
    }

    private function revert(array $manifest): int
    {
        $reverted = 0;

        foreach ($manifest['entries'] as $entry) {
            $reverted += $this->query($entry['table'])->where('id', $entry['id'])->where($entry['column'], $entry['new'])
                ->update([$entry['column'] => $entry['old']]);
        }

        $this->info("Pointed {$reverted} of ".count($manifest['entries']).' records back at their old files. The copies were kept.');

        return self::SUCCESS;
    }

    private function prune(array $manifest): int
    {
        $deleted = 0;
        $kept = 0;

        foreach ($manifest['entries'] as $entry) {
            $from = Storage::disk($entry['from_disk']);
            $record = $this->query($entry['table'])->where('id', $entry['id'])->first();

            $verified = $record !== null
                && $record->{$entry['column']} === $entry['new']
                && Storage::disk($manifest['media_disk'])->exists($entry['new'])
                && (! $from->exists($entry['from_path']) || Storage::disk($manifest['media_disk'])->size($entry['new']) === $from->size($entry['from_path']));

            // An old file can be shared by several records (the images that came with the site).
            if (! $verified || $this->stillUsed($entry['old']) || ($entry['from_disk'] === $manifest['media_disk'] && $entry['from_path'] === $entry['new'])) {
                $kept++;
                $this->warn("Kept {$entry['from_disk']}:{$entry['from_path']}: its copy or record could not be verified, or a record still uses it.");

                continue;
            }

            if ($from->exists($entry['from_path'])) {
                $from->delete($entry['from_path']);
                $deleted++;
            }
        }

        $this->info("Deleted {$deleted} old files; kept {$kept}.");

        return $kept > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function stillUsed(string $value): bool
    {
        return Cat::where('cat_image', $value)->orWhere('cat_clip', $value)->exists()
            || NewsEvent::where('eventimage', $value)->exists();
    }

    private function manifest(string $path): array
    {
        $json = $this->privateDisk()->get($path);
        $manifest = $json ? json_decode($json, true) : null;

        if (! is_array($manifest) || ! isset($manifest['entries'], $manifest['media_disk'])) {
            $this->fail("No migration manifest at {$path} on the private uploads disk.");
        }

        return $manifest;
    }

    private function query(string $table): Builder
    {
        if (! in_array($table, ['cats', 'news_events'], true)) {
            $this->fail("Unexpected table {$table} in the manifest.");
        }

        return DB::table($table);
    }

    private function privateDisk(): Filesystem
    {
        return Storage::disk(config('filesystems.private_uploads_disk'));
    }

    private function extension(string $name): string
    {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        return preg_match('/^[a-z0-9]{1,10}$/', $extension) ? $extension : 'bin';
    }
}
