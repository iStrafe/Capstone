<?php

namespace App\Console\Commands;

use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Models\NewsEvent;
use App\Support\PublicMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\SplFileInfo;

class MoveUploads extends Command
{
    protected $signature = 'app:move-uploads
                            {--from=* : Folder to move uploads out of (default: public/images and public/videos)}
                            {--dry-run : Only list what would be moved}
                            {--keep : Copy the files and leave the originals where they are}';

    protected $description = 'Move uploads out of public/ into storage: photos and clips to the media disk, applicant IDs and unused files to private storage';

    /** Site files that stay in public/images and in git. */
    private const SITE_FILES = ['placeholder.png'];

    public function handle(): int
    {
        $sources = $this->option('from') ?: [public_path('images'), public_path('videos')];
        $dryRun = (bool) $this->option('dry-run');

        $media = $this->mediaNames();
        $ids = $this->validIdNames();
        $private = Storage::disk(config('filesystems.private_uploads_disk'));

        $counts = ['media' => 0, 'id' => 0, 'unused' => 0, 'skipped' => 0];

        foreach ($sources as $source) {
            if (! is_dir($source)) {
                $this->line("Skipping {$source}: no such folder.");

                continue;
            }

            foreach (File::allFiles($source) as $file) {
                $relative = str_replace('\\', '/', $file->getRelativePathname());

                if (in_array($relative, self::SITE_FILES, true) && realpath($source) === realpath(public_path('images'))) {
                    continue;
                }

                // Applicant IDs win over everything else: they must never end up publicly readable.
                if (isset($ids[$file->getFilename()])) {
                    [$kind, $disk, $target] = ['id', $private, 'valid-ids/'.$file->getFilename()];
                } elseif (isset($media[$relative])) {
                    [$kind, $disk, $target] = ['media', PublicMedia::disk(), PublicMedia::path($relative)];
                } else {
                    [$kind, $disk, $target] = ['unused', $private, 'legacy-uploads/'.basename($source).'/'.$relative];
                }

                if ($this->output->isVerbose() || $dryRun) {
                    $this->line(sprintf('%-6s %s -> %s', $kind, $file->getPathname(), $target));
                }

                if ($dryRun) {
                    $counts[$kind]++;

                    continue;
                }

                if (! $this->copy($file, $disk, $target, $kind === 'media')) {
                    $counts['skipped']++;

                    continue;
                }

                $counts[$kind]++;

                if (! $this->option('keep')) {
                    File::delete($file->getPathname());
                }
            }

            if (! $dryRun && ! $this->option('keep')) {
                $this->removeEmptyFolders($source);
            }
        }

        $this->table(['Where', 'Files'], [
            ['Cat and news photos/clips -> media disk ('.PublicMedia::diskName().')', $counts['media']],
            ['Applicant ID photos -> private storage (valid-ids/)', $counts['id']],
            ['Not used by any record -> private storage (legacy-uploads/)', $counts['unused']],
            ['Skipped (a different file already has that name)', $counts['skipped']],
        ]);

        if ($dryRun) {
            $this->info('Dry run: nothing was moved.');

            return self::SUCCESS;
        }

        $this->linkPublicStorage();

        return $counts['skipped'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    /** @return array<string, true> photo and clip names the cats and news records point at */
    private function mediaNames(): array
    {
        return collect()
            ->merge(Cat::query()->pluck('cat_image'))
            ->merge(Cat::query()->pluck('cat_clip'))
            ->merge(NewsEvent::query()->pluck('eventimage'))
            ->filter()
            ->map(fn (string $name) => ltrim(str_replace('\\', '/', $name), '/'))
            ->flip()
            ->map(fn () => true)
            ->all();
    }

    /** @return array<string, true> file names of every uploaded ID */
    private function validIdNames(): array
    {
        // Raw column values: very old rows may hold a bare file name instead of a JSON list.
        return AdoptionRequest::query()
            ->toBase()
            ->whereNotNull('valid_id')
            ->pluck('valid_id')
            ->flatMap(fn ($value) => is_array($decoded = json_decode((string) $value, true)) ? $decoded : [$value])
            ->filter(fn ($file) => is_string($file) && trim($file) !== '')
            ->map(fn (string $file) => basename(str_replace('\\', '/', trim($file))))
            ->flip()
            ->map(fn () => true)
            ->all();
    }

    private function copy(SplFileInfo $file, $disk, string $target, bool $public): bool
    {
        if ($disk->exists($target)) {
            if ($disk->size($target) === $file->getSize()) {
                return true;
            }

            $this->warn("Not moving {$file->getPathname()}: {$target} already holds a different file.");

            return false;
        }

        $stream = fopen($file->getPathname(), 'rb');

        try {
            $written = $disk->writeStream($target, $stream, $public ? ['visibility' => 'public'] : []);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (! $written) {
            $this->error("Could not write {$target}.");

            return false;
        }

        return true;
    }

    private function removeEmptyFolders(string $source): void
    {
        foreach (File::directories($source) as $folder) {
            $this->removeEmptyFolders($folder);
        }

        // public/images keeps the placeholder; other emptied upload folders go away.
        if (realpath($source) !== realpath(public_path('images')) && File::isEmptyDirectory($source)) {
            File::deleteDirectory($source);
        }
    }

    /** The default media disk is served through public/storage, which `storage:link` creates. */
    private function linkPublicStorage(): void
    {
        $config = config('filesystems.disks.'.PublicMedia::diskName());

        if (($config['driver'] ?? null) === 'local' && ! file_exists(public_path('storage'))) {
            $this->call('storage:link');
        }
    }
}
