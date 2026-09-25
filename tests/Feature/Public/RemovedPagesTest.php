<?php

namespace Tests\Feature\Public;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RemovedPagesTest extends TestCase
{
    public static function removedPages(): array
    {
        return [
            'feed' => ['get', '/feed'],
            'home copy' => ['get', '/homecopy'],
            'test page' => ['get', '/test'],
            'report a cat form' => ['get', '/Services/report'],
            'report a cat submit' => ['post', '/Services/report'],
            'cat info dashboard' => ['get', '/admintestDashboard'],
            'cat info search' => ['get', '/admintest/search'],
            'rejected requests page' => ['get', '/RejectedRequest'],
            'all requests pdf' => ['get', '/adoption-requests/pdf'],
            'adopt by url' => ['get', '/cat/adopt/1'],
        ];
    }

    #[DataProvider('removedPages')]
    public function test_unfinished_pages_are_gone(string $method, string $uri): void
    {
        $admin = User::factory()->make()->forceFill(['id' => 1, 'role' => 'admin']);

        $this->withoutMiddleware(ValidateCsrfToken::class)
            ->actingAs($admin)
            ->{$method}($uri)
            ->assertNotFound();
    }
}
