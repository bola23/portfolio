<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFilterTest extends TestCase
{
    use RefreshDatabase;

    private Category $ecommerce;

    private Category $learning;

    private Technology $redis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ecommerce = Category::factory()->create(['name' => 'E-commerce', 'slug' => 'e-commerce']);
        $this->learning = Category::factory()->create(['name' => 'E-learning', 'slug' => 'e-learning']);
        $this->redis = Technology::factory()->create(['name' => 'Redis', 'slug' => 'redis']);
        $laravel = Technology::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

        Project::factory()->for($this->ecommerce)->create(['title' => 'Demo Shop'])
            ->technologies()->attach([$this->redis->id, $laravel->id]);
        Project::factory()->for($this->ecommerce)->create(['title' => 'Demo Marketplace'])
            ->technologies()->attach($laravel->id);
        Project::factory()->for($this->learning)->create(['title' => 'Demo Academy'])
            ->technologies()->attach($this->redis->id);
    }

    public function test_it_lists_all_published_projects_without_filters(): void
    {
        $this->get('/projects')
            ->assertOk()
            ->assertSee(['Demo Shop', 'Demo Marketplace', 'Demo Academy'])
            ->assertSee('3 projects');
    }

    public function test_it_filters_by_category(): void
    {
        $this->get('/projects?category=e-commerce')
            ->assertOk()
            ->assertSee(['Demo Shop', 'Demo Marketplace'])
            ->assertDontSee('Demo Academy');
    }

    public function test_it_filters_by_technology(): void
    {
        $this->get('/projects?tech=redis')
            ->assertOk()
            ->assertSee(['Demo Shop', 'Demo Academy'])
            ->assertDontSee('Demo Marketplace');
    }

    public function test_it_combines_category_and_technology_filters(): void
    {
        $this->get('/projects?category=e-commerce&tech=redis')
            ->assertOk()
            ->assertSee('Demo Shop')
            ->assertDontSee(['Demo Marketplace', 'Demo Academy']);
    }

    public function test_unknown_filters_show_an_empty_state(): void
    {
        $this->get('/projects?category=does-not-exist')
            ->assertOk()
            ->assertSee('No projects match these filters.');
    }

    public function test_malformed_filter_values_are_ignored(): void
    {
        $this->get('/projects?category=%3Cscript%3E&tech[]=x')
            ->assertOk()
            ->assertSee(['Demo Shop', 'Demo Marketplace', 'Demo Academy']);
    }

    public function test_filters_only_offer_categories_with_published_projects(): void
    {
        Category::factory()->create(['name' => 'Empty Domain', 'slug' => 'empty-domain']);
        $draftOnly = Category::factory()->create(['name' => 'Draft Domain', 'slug' => 'draft-domain']);
        Project::factory()->draft()->for($draftOnly)->create();

        $this->get('/projects')->assertDontSee('Empty Domain')->assertDontSee('Draft Domain');
    }
}
