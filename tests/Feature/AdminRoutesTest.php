<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    public static function adminRoutes(): array
    {
        return [
            'admin cat list' => ['get', '/cat'],
            'archive cat' => ['patch', '/admin/cats/1/archive'],
            'archived cats' => ['get', '/admin/cats/archived'],
            'admin dashboard' => ['get', '/adminDashboard'],
            'admin dashboard create' => ['get', '/adminDashboard/create'],
            'admin dashboard store' => ['post', '/adminDashboard'],
            'admin dashboard update' => ['put', '/adminDashboard/1'],
            'admin dashboard destroy' => ['delete', '/adminDashboard/1'],
            'cats index' => ['get', '/adminDashboard/cats'],
            'cats create' => ['get', '/adminDashboard/cats/create'],
            'cats store' => ['post', '/adminDashboard/cats'],
            'cats show' => ['get', '/adminDashboard/cats/1'],
            'cats edit' => ['get', '/adminDashboard/cats/1/edit'],
            'cats update' => ['put', '/adminDashboard/cats/1'],
            'cats destroy' => ['delete', '/adminDashboard/cats/1'],
            'adoption requests' => ['get', '/AdoptionRequest'],
            'rejected requests' => ['get', '/RejectedRequest'],
            'released requests' => ['get', '/ReleasedRequest'],
            'update request status' => ['post', '/update-status/1'],
            'valid ids' => ['get', '/view-valid-ids/1'],
            'single request pdf' => ['get', '/adoption-request/pdf/1'],
            'all requests pdf' => ['get', '/adoption-requests/pdf'],
            'analyze image form' => ['get', '/analyzeImage'],
            'analyze image' => ['post', '/analyzeImage'],
            'cat info dashboard' => ['get', '/admintestDashboard'],
            'cat info store' => ['post', '/admintestDashboard'],
            'cat info search' => ['get', '/admintest/search'],
            'news list' => ['get', '/news-events'],
            'news create' => ['get', '/news-events/create'],
            'news store' => ['post', '/news-events'],
            'news show' => ['get', '/news-events/1'],
            'news edit' => ['get', '/news-events/1/edit'],
            'news update' => ['put', '/news-events/1'],
            'news destroy' => ['delete', '/news-events/1'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_non_admin_users_are_forbidden(string $method, string $uri): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->actingAs($this->userWithRole('user'))
            ->{$method}($uri)
            ->assertForbidden();
    }

    #[DataProvider('adminRoutes')]
    public function test_guests_are_sent_to_login(string $method, string $uri): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->{$method}($uri)
            ->assertRedirect(route('login'));
    }

    public function test_admins_can_reach_admin_pages(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/adminDashboard/cats/create')
            ->assertOk();
    }

    // Unsaved users keep these tests off the database; the middleware only reads the role.
    private function userWithRole(string $role): User
    {
        $user = User::factory()->make()->forceFill(['id' => 1, 'role' => $role]);

        return $user;
    }
}
