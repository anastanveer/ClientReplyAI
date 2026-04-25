<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_200(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_home_page_contains_seo_headline(): void
    {
        $this->get(route('home'))
            ->assertSee('AI Reply Generator')
            ->assertSee('ClientReplyAI');
    }

    public function test_home_page_contains_key_sections(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('How it works');
        $response->assertSee('Before');
        $response->assertSee('Use cases');
        $response->assertSee('Features');
        $response->assertSee('Pricing');
    }

    public function test_home_page_links_to_register(): void
    {
        $this->get(route('home'))
            ->assertSee(route('register'));
    }

    public function test_home_page_shows_open_app_link_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('home'))
            ->assertSee('Open App')
            ->assertSee(route('dashboard'));
    }

    public function test_pricing_page_returns_200(): void
    {
        $this->get(route('pricing'))->assertOk();
    }

    public function test_pricing_page_shows_free_and_pro_plans(): void
    {
        $this->get(route('pricing'))
            ->assertSee('Free')
            ->assertSee('$0')
            ->assertSee('Pro')
            ->assertSee('$9');
    }

    public function test_pricing_page_shows_open_app_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('pricing'))
            ->assertSee('Open App');
    }
}
