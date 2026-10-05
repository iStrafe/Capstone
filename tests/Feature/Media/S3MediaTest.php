<?php

namespace Tests\Feature\Media;

use App\Models\Cat;
use App\Models\User;
use App\Support\PublicMedia;
use Aws\S3\S3Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The media flow on a real S3 API. Needs an S3-compatible endpoint (MinIO, moto, LocalStack)
 * in S3_TEST_ENDPOINT, e.g. `S3_TEST_ENDPOINT=http://127.0.0.1:9100 php artisan test`;
 * skipped otherwise. Each test uses a fresh bucket.
 */
class S3MediaTest extends TestCase
{
    use RefreshDatabase;

    private string $bucket;

    protected function setUp(): void
    {
        parent::setUp();

        $endpoint = env('S3_TEST_ENDPOINT');

        if (! $endpoint) {
            $this->markTestSkipped('Set S3_TEST_ENDPOINT to an S3-compatible endpoint to run the S3 tests.');
        }

        $this->bucket = 'aducats-test-'.Str::lower(Str::random(8));

        config([
            'filesystems.disks.s3' => array_merge(config('filesystems.disks.s3'), [
                'key' => env('S3_TEST_KEY', 'test'),
                'secret' => env('S3_TEST_SECRET', 'test'),
                'region' => 'ap-southeast-1',
                'bucket' => $this->bucket,
                'endpoint' => $endpoint,
                'use_path_style_endpoint' => true,
            ]),
            'filesystems.media_disk' => 's3',
            'filesystems.private_uploads_disk' => 's3',
            'filesystems.media_signed_urls' => true,
        ]);

        $this->client()->createBucket(['Bucket' => $this->bucket, 'CreateBucketConfiguration' => ['LocationConstraint' => 'ap-southeast-1']]);
        // The default for new buckets on AWS: ACLs disabled.
        $this->client()->putBucketOwnershipControls(['Bucket' => $this->bucket, 'OwnershipControls' => ['Rules' => [['ObjectOwnership' => 'BucketOwnerEnforced']]]]);
    }

    private function client(): S3Client
    {
        return Storage::disk('s3')->getClient();
    }

    private function keys(): array
    {
        return Storage::disk('s3')->allFiles();
    }

    public function test_upload_store_and_retrieve_through_s3(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $photo = UploadedFile::fake()->image('Mochi.jpg', 400, 300);

        $this->actingAs($admin)->post(route('admin.cats.store'), [
            'cat_name' => 'Mochi', 'sex' => 'Female',
            'cat_image' => $photo,
            'cat_clip' => UploadedFile::fake()->create('clip.mp4', 300, 'video/mp4'),
        ])->assertSessionHasNoErrors();

        $cat = Cat::sole();
        $this->assertEqualsCanonicalizing([$cat->cat_image, $cat->cat_clip], $this->keys());
        $this->assertStringStartsWith("cats/{$cat->id}/images/", $cat->cat_image);
        $this->assertStringStartsWith("cats/{$cat->id}/videos/", $cat->cat_clip);

        $head = $this->client()->headObject(['Bucket' => $this->bucket, 'Key' => $cat->cat_image]);
        $this->assertSame('image/jpeg', $head['ContentType']);
        $this->assertSame('public, max-age=31536000, immutable', $head['CacheControl']);
        $this->assertSame('video/mp4', $this->client()->headObject(['Bucket' => $this->bucket, 'Key' => $cat->cat_clip])['ContentType']);

        // No grant to everyone: the objects aren't public.
        $grants = $this->client()->getObjectAcl(['Bucket' => $this->bucket, 'Key' => $cat->cat_image])['Grants'];
        $this->assertEmpty(collect($grants)->filter(fn ($grant) => str_contains($grant['Grantee']['URI'] ?? '', 'AllUsers')));

        // The page gets a presigned URL, the same all hour, that loads the photo.
        $this->travelTo(now()->startOfHour()->addMinutes(5));
        $first = null;
        $this->get(route('cats.show', $cat))->assertInertia(function (Assert $page) use (&$first) {
            $first = $page->toArray()['props']['cat']['image'];
        });
        $this->travel(40)->minutes();
        $this->get(route('cats.show', $cat))->assertInertia(fn (Assert $page) => $page->where('cat.image', $first));

        $this->assertStringContainsString('X-Amz-Signature=', $first);
        $this->assertStringContainsString('X-Amz-Expires=10800', $first);
        $this->assertStringContainsString($cat->cat_image, $first);
        $this->assertStringNotContainsString('Mochi', $first);

        $response = Http::get($first);
        $this->assertTrue($response->successful());
        $this->assertSame(file_get_contents($photo->getRealPath()), $response->body());

        // Replacing the photo removes the old object; deleting the cat removes the rest.
        $this->put(route('admin.cats.update', $cat), [
            'cat_name' => 'Mochi', 'sex' => 'Female', 'status' => 'Active', 'cat_image' => UploadedFile::fake()->image('again.png'),
        ])->assertSessionHasNoErrors();
        $this->assertEqualsCanonicalizing([$cat->fresh()->cat_image, $cat->cat_clip], $this->keys());

        $this->delete(route('admin.cats.destroy', $cat));
        $this->assertSame([], $this->keys());
    }

    public function test_existing_local_files_migrate_to_the_bucket(): void
    {
        Storage::fake('public')->put('images/1730882812.png', 'photo');
        Storage::fake('local');
        $cat = Cat::create(['cat_name' => 'Mingming', 'sex' => 'Female', 'cat_image' => '1730882812.png']);

        $this->artisan('app:migrate-media', ['--from' => 'public'])->assertSuccessful();

        $key = $cat->fresh()->cat_image;
        $this->assertStringStartsWith("cats/{$cat->id}/images/", $key);
        $this->assertSame('photo', Http::get(PublicMedia::url($key))->body());
        $this->assertSame('image/png', $this->client()->headObject(['Bucket' => $this->bucket, 'Key' => $key])['ContentType']);
        Storage::disk('public')->assertExists('images/1730882812.png');
    }

    public function test_applicant_ids_on_the_bucket_are_only_served_to_admins(): void
    {
        Storage::disk('s3')->put('valid-ids/front.jpg', 'id photo');

        $this->get(route('validIdFile', 'front.jpg'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('validIdFile', 'front.jpg'))->assertForbidden();

        $response = $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('validIdFile', 'front.jpg'));
        $response->assertOk();
        $this->assertSame('id photo', $response->streamedContent());
    }
}
