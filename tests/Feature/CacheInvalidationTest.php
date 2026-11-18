<?php

namespace Tests\Feature;

use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\Technology;
use App\Services\PortfolioContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Use a real persistent store so cached content survives between requests.
        config(['cache.default' => 'file']);
        cache()->store('file')->flush();

        // Create the singletons up front; creating them later would itself flush the cache.
        SiteSetting::current();
        Profile::current();
    }

    public function test_public_content_is_served_from_cache_between_requests(): void
    {
        Project::factory()->create(['title' => 'Demo Cached']);
        $this->get('/projects')->assertSee('Demo Cached');

        // A raw query bypasses model events, so the cached copy is still served.
        Project::query()->toBase()->update(['title' => 'Demo Bypassed']);

        $this->get('/projects')->assertSee('Demo Cached')->assertDontSee('Demo Bypassed');
    }

    public function test_model_changes_invalidate_the_cache(): void
    {
        $this->get('/')->assertOk();

        Profile::query()->first()->update(['headline' => 'Demo Updated Headline']);

        $this->get('/')->assertSee('Demo Updated Headline');
    }

    public function test_pivot_only_changes_from_the_admin_invalidate_the_cache(): void
    {
        $project = Project::factory()->create();
        $technology = Technology::factory()->create(['name' => 'Demo Tech Added', 'slug' => 'demo-tech-added']);
        $this->get('/projects')->assertDontSee('Demo Tech Added');

        $this->signInAsAdmin();
        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->fillForm(['technologies' => [$technology->id]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/projects')->assertSee('Demo Tech Added');
    }

    public function test_social_link_changes_invalidate_the_cache(): void
    {
        $link = SocialLink::factory()->create(['url' => 'https://github.com/demo-before']);
        $this->get('/')->assertSee('https://github.com/demo-before', false);

        $link->update(['url' => 'https://github.com/demo-after']);
        app()->forgetScopedInstances();

        $this->get('/')->assertSee('https://github.com/demo-after', false);
        $this->assertSame('https://github.com/demo-after', app(PortfolioContent::class)->socialLinks()->first()->url);
    }
}
