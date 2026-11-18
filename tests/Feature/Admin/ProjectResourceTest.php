<?php

namespace Tests\Feature\Admin;

use App\Enums\ProjectType;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->signInAsAdmin();
    }

    public function test_it_lists_projects_including_drafts(): void
    {
        $projects = collect([
            Project::factory()->create(),
            Project::factory()->draft()->create(),
        ]);

        Livewire::test(ListProjects::class)
            ->assertOk()
            ->assertCanSeeTableRecords($projects);
    }

    public function test_it_creates_a_project_with_relations_and_an_optimized_thumbnail(): void
    {
        $category = Category::factory()->create();
        $technologies = Technology::factory()->count(2)->create();

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Demo Created From Admin',
                'slug' => 'demo-created-from-admin',
                'excerpt' => 'Created through the Filament form.',
                'category_id' => $category->id,
                'type' => ProjectType::Api->value,
                'technologies' => $technologies->pluck('id')->all(),
                'overview' => '<p>Rich overview</p>',
                'features' => [['feature' => 'First feature'], ['feature' => 'Second feature']],
                'live_url' => 'https://example.com',
                'repo_url' => 'https://github.com/example/demo',
                'thumbnail' => UploadedFile::fake()->image('cover.jpg', 2400, 1500),
                'is_published' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::query()->where('slug', 'demo-created-from-admin')->sole();

        $this->assertSame($category->id, $project->category_id);
        $this->assertSame(ProjectType::Api, $project->type);
        $this->assertEqualsCanonicalizing($technologies->pluck('id')->all(), $project->technologies->pluck('id')->all());
        $this->assertSame(['First feature', 'Second feature'], $project->features);
        $this->assertTrue($project->is_featured);
        $this->assertNotNull($project->published_at);

        // Uploaded images are converted to WebP with a small responsive variant.
        $this->assertStringEndsWith('.webp', $project->thumbnail);
        Storage::disk('public')->assertExists([$project->thumbnail, str_replace('.webp', '-sm.webp', $project->thumbnail)]);

        $this->get(route('projects.show', $project))->assertOk()->assertSee('Rich overview');
    }

    public function test_it_validates_required_fields_and_unique_slugs(): void
    {
        Project::factory()->create(['slug' => 'taken-slug']);

        Livewire::test(CreateProject::class)
            ->fillForm(['title' => '', 'slug' => 'taken-slug', 'excerpt' => '', 'live_url' => 'not-a-url'])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required', 'slug' => 'unique', 'excerpt' => 'required', 'live_url' => 'url']);
    }

    public function test_it_updates_a_project_and_the_public_site_reflects_it(): void
    {
        $project = Project::factory()->create(['title' => 'Demo Old Title']);
        $this->get('/projects')->assertSee('Demo Old Title');

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->fillForm(['title' => 'Demo New Title', 'is_published' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Demo New Title', $project->fresh()->title);
        $this->get('/projects')->assertSee('Demo New Title')->assertDontSee('Demo Old Title');
    }

    public function test_it_shows_the_project_details_page(): void
    {
        $project = Project::factory()->create(['title' => 'Demo Viewable']);

        Livewire::test(ViewProject::class, ['record' => $project->getRouteKey()])
            ->assertOk()
            ->assertSee('Demo Viewable');
    }

    public function test_it_soft_deletes_restores_and_force_deletes_projects(): void
    {
        Storage::disk('public')->put('projects/cover.webp', 'image');
        Storage::disk('public')->put('projects/cover-sm.webp', 'image');
        $project = Project::factory()->create(['thumbnail' => 'projects/cover.webp']);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])->callAction('delete');
        $this->assertSoftDeleted($project);
        Storage::disk('public')->assertExists('projects/cover.webp');

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])->callAction('restore');
        $this->assertNotSoftDeleted($project);

        $project->delete();
        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])->callAction('forceDelete');
        $this->assertModelMissing($project);
        Storage::disk('public')->assertMissing(['projects/cover.webp', 'projects/cover-sm.webp']);
    }

    public function test_bulk_publish_and_unpublish(): void
    {
        $drafts = Project::factory()->draft()->count(2)->create();

        Livewire::test(ListProjects::class)
            ->selectTableRecords($drafts->pluck('id')->all())
            ->callAction(TestAction::make('publish')->table()->bulk());

        $this->assertTrue($drafts->every(fn (Project $project) => $project->fresh()->is_published));

        Livewire::test(ListProjects::class)
            ->selectTableRecords($drafts->pluck('id')->all())
            ->callAction(TestAction::make('unpublish')->table()->bulk());

        $this->assertTrue($drafts->every(fn (Project $project) => ! $project->fresh()->is_published));
    }

    public function test_display_order_can_be_changed_by_reordering(): void
    {
        $first = Project::factory()->create(['sort_order' => 1]);
        $second = Project::factory()->create(['sort_order' => 2]);

        Livewire::test(ListProjects::class)
            ->call('reorderTable', [(string) $second->id, (string) $first->id]);

        $this->assertLessThan($first->fresh()->sort_order, $second->fresh()->sort_order);
    }
}
