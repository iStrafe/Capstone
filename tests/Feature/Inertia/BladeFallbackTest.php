<?php

namespace Tests\Feature\Inertia;

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\InertiaBladeFallback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

/**
 * An Inertia visit that lands on a Blade page becomes a full page load (409 + X-Inertia-Location).
 */
class BladeFallbackTest extends TestCase
{
    use RefreshDatabase;

    /** Headers the Inertia client sends, with the current asset version so no version-mismatch 409 muddies the result. */
    private function inertiaHeaders(): array
    {
        return [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-Inertia-Version' => (string) app(HandleInertiaRequests::class)->version(request()),
        ];
    }

    public function test_inertia_visit_to_a_blade_page_asks_for_a_full_page_load(): void
    {
        // The admin pages are still Blade.
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->withHeaders($this->inertiaHeaders())
            ->get('/admin/messages')
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', url('/admin/messages'));
    }

    public function test_the_full_url_with_query_string_is_kept(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->withHeaders($this->inertiaHeaders())
            ->get('/admin/messages?page=2')
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', url('/admin/messages?page=2'));
    }

    public function test_inertia_visit_to_a_react_page_still_gets_the_page_json(): void
    {
        $this->withHeaders($this->inertiaHeaders())
            ->get('/')
            ->assertOk()
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'Home');
    }

    public function test_plain_browser_visits_to_blade_pages_are_untouched(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/messages')
            ->assertOk()
            ->assertHeaderMissing('X-Inertia-Location');
    }

    public function test_redirects_are_left_alone(): void
    {
        $this->withHeaders($this->inertiaHeaders())
            ->get('/myRequest')
            ->assertRedirect(route('login'));

        $this->withHeaders($this->inertiaHeaders())
            ->get('/userDashboard')
            ->assertRedirect(route('home'));
    }

    public function test_only_successful_html_responses_to_inertia_gets_are_converted(): void
    {
        $middleware = new InertiaBladeFallback;
        $inertiaGet = Request::create('/somewhere', 'GET', server: ['HTTP_X_INERTIA' => 'true']);
        $html = fn () => new Response('<html></html>', 200, ['Content-Type' => 'text/html; charset=UTF-8']);

        $converted = $middleware->handle($inertiaGet, $html);
        $this->assertSame(409, $converted->getStatusCode());
        $this->assertSame('http://localhost/somewhere', $converted->headers->get('X-Inertia-Location'));

        $untouched = [
            'json' => [$inertiaGet, fn () => new JsonResponse(['ok' => true])],
            'inertia' => [$inertiaGet, fn () => new JsonResponse(['component' => 'Home'], 200, ['X-Inertia' => 'true'])],
            'redirect' => [$inertiaGet, fn () => new RedirectResponse('/elsewhere')],
            'download' => [$inertiaGet, fn () => new Response('a,b', 200, ['Content-Type' => 'text/html', 'Content-Disposition' => 'attachment; filename=a.csv'])],
            'file' => [$inertiaGet, fn () => new BinaryFileResponse(public_path('images/placeholder.png'))],
            'stream' => [$inertiaGet, fn () => new StreamedResponse(fn () => null, 200, ['Content-Type' => 'text/html'])],
            'error page' => [$inertiaGet, fn () => new Response('<html>Not found</html>', 404, ['Content-Type' => 'text/html'])],
            'plain text' => [$inertiaGet, fn () => new Response('ok', 200, ['Content-Type' => 'text/plain'])],
            'non-inertia request' => [Request::create('/somewhere'), $html],
            'inertia post' => [Request::create('/somewhere', 'POST', server: ['HTTP_X_INERTIA' => 'true']), $html],
        ];

        foreach ($untouched as $case => [$request, $next]) {
            $response = $middleware->handle($request, $next);
            $this->assertNotSame(409, $response->getStatusCode(), $case);
            $this->assertFalse($response->headers->has('X-Inertia-Location'), $case);
        }
    }

    public function test_flash_messages_survive_the_extra_full_page_load(): void
    {
        // A React form posts, the controller redirects back to a Blade page with a flash message.
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->withHeaders($this->inertiaHeaders())
            ->from('/admin/messages')
            ->post('/contact', [
                'full_name' => 'Juan Dela Cruz',
                'mobile_number' => '09171234567',
                'message' => 'Hello!',
            ])
            ->assertRedirect('/admin/messages');

        // Inertia follows the redirect over XHR and gets told to load the page in full...
        $this->withHeaders($this->inertiaHeaders())
            ->get('/admin/messages')
            ->assertStatus(409);

        // ...and the full page load still shows the message.
        $this->flushHeaders()
            ->get('/admin/messages')
            ->assertOk()
            ->assertSee('Your message reached the AduCats team.');
    }
}
