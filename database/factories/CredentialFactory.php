<?php

namespace Database\Factories;

use App\Enums\CredentialType;
use App\Models\Credential;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Credential>
 */
class CredentialFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(CredentialType::cases()),
            'title' => 'Demo '.fake()->words(3, true),
            'issuer' => 'Demo Institute',
            'start_year' => null,
            'end_year' => (int) fake()->year(),
            'url' => null,
            'sort_order' => 0,
            'is_visible' => true,
        ];
    }
}
