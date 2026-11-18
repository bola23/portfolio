<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skill_category_id' => SkillCategory::factory(),
            'name' => Str::title(fake()->unique()->word()),
            'icon' => null,
            'icon_path' => null,
            'proficiency' => fake()->optional()->numberBetween(50, 100),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_visible' => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_visible' => false]);
    }
}
