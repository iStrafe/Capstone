<?php

namespace Tests\Feature\Public;

use App\Models\Cat;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Fixes from the user-side end-to-end test of 2026-10-02.
 */
class UserHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function cat(): Cat
    {
        return Cat::create(['cat_name' => 'Mingming', 'age' => 2, 'color' => 'Orange', 'breed' => 'Puspin', 'sex' => 'Female']);
    }

    private function adoption(array $overrides = [])
    {
        Storage::fake('local');

        return $this->actingAs(User::factory()->create())->post('/AdoptionForm', array_merge([
            'cat_id' => $this->cat()->id,
            'name' => 'Juan Dela Cruz',
            'address' => '123 Rizal St, Manila',
            'email' => 'juan@example.com',
            'phone' => '0917 123 4567',
            'date_of_adoption' => now()->addWeek()->toDateString(),
            'valid_id' => [UploadedFile::fake()->image('school-id.jpg')],
            'terms' => '1',
        ], $overrides));
    }

    private function contact(array $overrides = [])
    {
        return $this->from(route('contactus'))->post(route('contact.store'), array_merge([
            'full_name' => 'Maria Clara',
            'mobile_number' => '0917 123 4567',
            'message' => 'Is Mingming still available?',
        ], $overrides));
    }

    public function test_ids_too_big_for_the_database_are_a_friendly_404(): void
    {
        $this->get('/cat/99999999999999999999')->assertNotFound()->assertSee('Page not found')->assertSee('Back to home');
        $this->actingAs(User::factory()->create())->get('/cat/99999999999999999999/adopt')->assertNotFound();
    }

    public function test_error_pages_have_a_way_back(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('Back to home')->assertSee('See cats for adoption');
        $this->actingAs(User::factory()->create())->get('/adminDashboard/cats')->assertForbidden()->assertSee('You can’t open this page');
    }

    public function test_page_history_is_encrypted_and_cleared_on_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertInertia(fn (Assert $page) => $page->component('Profile/Edit'));
        $this->assertTrue($this->get(route('profile.edit'))->viewData('page')['encryptHistory']);

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertTrue($this->get('/')->viewData('page')['clearHistory']);
    }

    public function test_google_sign_in_without_keys_stays_on_the_site(): void
    {
        config(['services.google.client_id' => null, 'services.google.client_secret' => null]);

        $this->get(route('google-auth'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Google sign-in isn’t available right now. Please log in with your email and password.');

        $this->get(route('login'))->assertInertia(fn (Assert $page) => $page->where('links.google', null));
    }

    public function test_the_google_button_shows_when_google_is_set_up(): void
    {
        config(['services.google.client_id' => 'id', 'services.google.client_secret' => 'secret']);

        $this->get(route('login'))->assertInertia(fn (Assert $page) => $page->where('links.google', route('google-auth')));
    }

    public function test_phone_numbers_must_be_digits(): void
    {
        foreach (['abcdefg', '!!!!!', '0', '-12345', 'call me maybe'] as $bad) {
            $this->contact(['mobile_number' => $bad])->assertSessionHasErrors(['mobile_number' => 'Enter a phone number using digits, like 0917 123 4567.']);
        }

        $this->contact(['mobile_number' => '+63 (917) 123-4567'])->assertSessionHasNoErrors();
        $this->assertSame('+639171234567', Contact::sole()->mobile_number);

        $this->adoption(['phone' => 'call me'])->assertSessionHasErrors(['phone' => 'Enter a phone number using digits, like 0917 123 4567.']);
    }

    public function test_the_adoption_phone_stays_optional(): void
    {
        $this->adoption(['phone' => ''])->assertSessionHasNoErrors();
    }

    public function test_contact_messages_have_a_limit(): void
    {
        $this->contact(['message' => str_repeat('a', 5001)])->assertSessionHasErrors('message');
        $this->contact(['message' => str_repeat('a', 5000)])->assertSessionHasNoErrors();
    }

    public function test_nul_characters_are_removed_instead_of_cutting_the_text(): void
    {
        $this->contact(['message' => "abc\0def"])->assertSessionHasNoErrors();

        $this->assertSame('abcdef', Contact::sole()->message);
    }

    public function test_pickup_day_is_within_three_months(): void
    {
        $this->adoption(['date_of_adoption' => now()->addMonths(3)->addDay()->toDateString()])
            ->assertSessionHasErrors(['date_of_adoption' => 'Choose a day within the next 3 months.']);
        $this->adoption(['date_of_adoption' => '9999-12-31'])->assertSessionHasErrors('date_of_adoption');
    }

    public function test_the_request_page_gets_the_last_pickup_day(): void
    {
        $cat = $this->cat();

        $this->actingAs(User::factory()->create())->get(route('adoption.start', $cat))
            ->assertInertia(fn (Assert $page) => $page->where('latestPickup', now()->addMonths(3)->toDateString()));
    }

    public function test_an_id_photo_php_dropped_explains_why(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'id');

        $this->adoption(['valid_id' => [new UploadedFile($path, 'id.jpg', 'image/jpeg', UPLOAD_ERR_CANT_WRITE, true)]])
            ->assertSessionHasErrors(['valid_id.0' => 'The ID photo didn’t upload because PHP couldn’t save it in its temporary folder. Set upload_tmp_dir in php.ini to a folder PHP can write to.']);

        @unlink($path);
    }

    public function test_donations_have_an_upper_limit(): void
    {
        $this->from('/')->post(route('paymongo.create'), ['amount' => '100001', 'description' => 'Gift'])
            ->assertSessionHasErrors(['amount' => 'For gifts over ₱100,000, please contact us directly.']);
    }

    public function test_a_repeat_reset_request_gets_the_same_answer(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $generic = 'If an account uses that email, a link to choose a new password is on its way. Check your inbox.';

        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status', $generic);
        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHasNoErrors()->assertSessionHas('status', $generic);
    }
}
