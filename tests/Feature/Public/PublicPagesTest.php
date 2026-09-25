<?php

namespace Tests\Feature\Public;

use App\Models\Cat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_adoption_page_renders_when_there_are_no_cats(): void
    {
        $this->get(route('adoptCat'))->assertOk();
    }

    public function test_each_cat_card_carries_its_own_video(): void
    {
        Cat::create(['cat_name' => 'Mingming', 'age' => 2, 'color' => 'Orange', 'breed' => 'Puspin', 'sex' => 'Female', 'cat_clip' => 'first.mp4']);
        Cat::create(['cat_name' => 'Tiger', 'age' => 3, 'color' => 'Black', 'breed' => 'Puspin', 'sex' => 'Male']);

        $this->get(route('adoptCat'))
            ->assertOk()
            ->assertSee('data-clip="'.asset('images/first.mp4').'"', false)
            ->assertSee('data-clip=""', false);
    }

    public function test_home_page_does_not_inline_the_unused_stylesheet(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $this->assertStringNotContainsString('Start Bootstrap - Agency', $response->getContent());
        $this->assertLessThan(200_000, strlen($response->getContent()));
    }
}
