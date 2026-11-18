<?php

namespace Database\Factories;

use App\Enums\SocialPlatform;
use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialLink>
 */
class SocialLinkFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => SocialPlatform::GitHub,
            'label' => null,
            'url' => 'https://github.com/'.fake()->userName(),
            'sort_order' => 0,
            'is_visible' => true,
            'show_in_hero' => true,
        ];
    }
}
