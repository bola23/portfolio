<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_the_full_case_study_from_the_database(): void
    {
        $project = Project::factory()
            ->for(Category::factory()->create(['name' => 'Delivery & Logistics']))
            ->create([
                'title' => 'Demo Dispatch Platform',
                'slug' => 'demo-dispatch-platform',
                'excerpt' => 'A dispatching backend.',
                'overview' => '<p>Overview copy.</p>',
                'problem' => '<p>Problem copy.</p>',
                'solution' => '<p>Solution copy.</p>',
                'responsibilities' => '<ul><li>Responsibility copy</li></ul>',
                'architecture' => '<p>Architecture copy.</p>',
                'features' => ['Automated dispatch', 'Driver app API'],
                'live_url' => 'https://example.com/live',
                'repo_url' => 'https://github.com/example/repo',
                'meta_title' => 'Custom SEO Title',
            ]);
        $project->technologies()->attach(Technology::factory()->create(['name' => 'Laravel']));
        ProjectImage::factory()->for($project)->create(['caption' => 'Dashboard screenshot']);

        $this->get('/projects/demo-dispatch-platform')
            ->assertOk()
            ->assertSee('<title>Custom SEO Title', false)
            ->assertSee(['Demo Dispatch Platform', 'A dispatching backend.', 'Delivery &amp; Logistics'], false)
            ->assertSee(['The Challenge', 'The Solution', 'Technical Details'])
            ->assertSee(['Overview copy.', 'Problem copy.', 'Solution copy.', 'Responsibility copy', 'Architecture copy.'])
            ->assertSee(['Automated dispatch', 'Driver app API', 'Laravel', 'Dashboard screenshot'])
            ->assertSee('https://example.com/live', false)
            ->assertSee('https://github.com/example/repo', false)
            ->assertSee('"@type":"CreativeWork"', false);
    }

    public function test_empty_sections_are_hidden(): void
    {
        $project = Project::factory()->create(['overview' => '<p>Only an overview.</p>', 'problem' => null, 'architecture' => '<p></p>']);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Only an overview.')
            ->assertDontSee('The Challenge')
            ->assertDontSee('Technical Details')
            ->assertDontSee('id="gallery"', false);
    }

    public function test_rich_text_is_sanitized_before_rendering(): void
    {
        $project = Project::factory()->create([
            'overview' => '<p>Safe text</p><script>alert("x")</script><img src=x onerror="alert(1)">',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Safe text')
            ->assertDontSee('<script>alert("x")</script>', false)
            ->assertDontSee('onerror', false);
    }

    public function test_it_links_to_adjacent_projects(): void
    {
        $first = Project::factory()->create(['title' => 'Demo Alpha', 'sort_order' => 1]);
        Project::factory()->create(['title' => 'Demo Beta', 'sort_order' => 2]);

        $this->get(route('projects.show', $first))->assertSee('Next project →')->assertSee('Demo Beta');
    }

    public function test_unknown_slugs_return_404(): void
    {
        $this->get('/projects/does-not-exist')->assertNotFound()->assertSee('Lost in the');
    }
}
