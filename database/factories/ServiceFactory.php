<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(3, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'icon' => 'ui-code',
            'summary' => fake()->sentence(12),
            'description' => null,
            'deliverables' => null,
            'sort_order' => fake()->numberBetween(0, 10),
            'is_visible' => true,
        ];
    }
}
