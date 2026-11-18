<?php

namespace Tests\Feature;

use App\Enums\SocialPlatform;
use App\Models\SocialLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_hero_shows_only_social_links_flagged_for_the_hero(): void
    {
        SocialLink::factory()->create(['url' => 'https://github.com/demo-hero-user', 'show_in_hero' => true]);
        SocialLink::factory()->create([
            'platform' => SocialPlatform::LinkedIn,
            'url' => 'https://www.linkedin.com/in/demo-footer-only',
            'show_in_hero' => false,
        ]);

        $html = $this->get('/')->assertOk()->getContent();
        $hero = str($html)->after('id="home"')->before('id="about"')->toString();

        $this->assertStringContainsString('https://github.com/demo-hero-user', $hero);
        $this->assertStringNotContainsString('demo-footer-only', $hero);

        // Links hidden from the hero still appear elsewhere on the page.
        $this->assertStringContainsString('https://www.linkedin.com/in/demo-footer-only', $html);
    }

    public function test_hidden_social_links_are_not_shown_anywhere(): void
    {
        SocialLink::factory()->create(['url' => 'https://github.com/demo-hidden-user', 'is_visible' => false]);

        $this->get('/')->assertOk()->assertDontSee('demo-hidden-user');
    }
}
