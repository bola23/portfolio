<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_projects_appear_on_the_public_site(): void
    {
        $project = Project::factory()->featured()->create(['title' => 'Demo Visible Platform']);

        $this->get('/')->assertOk()->assertSee('Demo Visible Platform');
        $this->get('/projects')->assertOk()->assertSee('Demo Visible Platform');
        $this->get(route('projects.show', $project))->assertOk()->assertSee('Demo Visible Platform');
        $this->get('/sitemap.xml')->assertOk()->assertSee(route('projects.show', $project), false);
    }

    public function test_draft_projects_are_never_publicly_accessible(): void
    {
        $draft = Project::factory()->draft()->featured()->create(['title' => 'Demo Secret Draft']);

        $this->get('/')->assertOk()->assertDontSee('Demo Secret Draft');
        $this->get('/projects')->assertOk()->assertDontSee('Demo Secret Draft');
        $this->get('/projects/'.$draft->slug)->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertDontSee($draft->slug);
    }

    public function test_publishing_a_draft_makes_it_visible_immediately(): void
    {
        $draft = Project::factory()->draft()->create(['title' => 'Demo Soon Live']);
        $this->get('/projects')->assertDontSee('Demo Soon Live');

        $draft->update(['is_published' => true]);

        $this->get('/projects')->assertSee('Demo Soon Live');
        $this->assertNotNull($draft->fresh()->published_at);
    }

    public function test_unpublishing_hides_a_project_immediately(): void
    {
        $project = Project::factory()->create(['title' => 'Demo Going Dark']);
        $this->get('/projects')->assertSee('Demo Going Dark');

        $project->update(['is_published' => false]);

        $this->get('/projects')->assertDontSee('Demo Going Dark');
        $this->get('/projects/'.$project->slug)->assertNotFound();
    }

    public function test_soft_deleted_projects_are_not_accessible(): void
    {
        $project = Project::factory()->create(['title' => 'Demo Removed']);
        $this->get('/projects/'.$project->slug)->assertOk();

        $project->delete();

        $this->get('/projects/'.$project->slug)->assertNotFound();
        $this->get('/projects')->assertDontSee('Demo Removed');
        $this->assertSoftDeleted($project);
    }

    public function test_display_order_controls_public_order(): void
    {
        Project::factory()->create(['title' => 'Demo Second', 'sort_order' => 2]);
        Project::factory()->create(['title' => 'Demo First', 'sort_order' => 1]);

        $this->get('/projects')->assertSeeInOrder(['Demo First', 'Demo Second']);

        Project::query()->where('title', 'Demo Second')->first()->update(['sort_order' => 0]);

        $this->get('/projects')->assertSeeInOrder(['Demo Second', 'Demo First']);
    }

    public function test_only_featured_projects_are_shown_on_the_home_page(): void
    {
        Project::factory()->featured()->create(['title' => 'Demo Featured One']);
        Project::factory()->create(['title' => 'Demo Not Featured']);

        $this->get('/')->assertSee('Demo Featured One')->assertDontSee('Demo Not Featured');
    }

    public function test_admins_can_preview_drafts_but_they_are_marked_noindex(): void
    {
        $draft = Project::factory()->draft()->create(['title' => 'Demo Preview Draft']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/projects/'.$draft->slug)
            ->assertOk()
            ->assertSee('Draft preview')
            ->assertSee('noindex, nofollow', false);
    }

    public function test_regular_users_cannot_preview_drafts(): void
    {
        $draft = Project::factory()->draft()->create();

        $this->actingAs(User::factory()->create())
            ->get('/projects/'.$draft->slug)
            ->assertNotFound();
    }
}
