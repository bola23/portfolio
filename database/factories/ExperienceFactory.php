<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-5 years', '-1 year');

        return [
            'company' => 'Demo Company '.fake()->unique()->numberBetween(1, 999),
            'company_url' => null,
            'position' => 'Backend Developer',
            'employment_type' => 'Full-time',
            'work_mode' => 'Remote',
            'location' => null,
            'start_date' => $start,
            'end_date' => fake()->dateTimeBetween($start, 'now'),
            'is_current' => false,
            'description' => fake()->sentence(16),
            'responsibilities' => [fake()->sentence(), fake()->sentence()],
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }

    public function current(): static
    {
        return $this->state(fn (): array => ['is_current' => true, 'end_date' => null]);
    }
}
