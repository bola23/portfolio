<?php

namespace Database\Factories;

use App\Enums\ProjectType;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'Demo '.Str::title(fake()->unique()->words(3, true));

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(14),
            'type' => fake()->randomElement(ProjectType::cases()),
            'region' => null,
            'role' => 'Backend Developer',
            'overview' => '<p>'.fake()->paragraph().'</p>',
            'problem' => null,
            'solution' => null,
            'responsibilities' => null,
            'architecture' => null,
            'features' => null,
            'thumbnail' => null,
            'live_url' => null,
            'repo_url' => null,
            'is_featured' => false,
            'is_published' => true,
            'published_at' => now(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['is_published' => false, 'published_at' => null]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }
}
