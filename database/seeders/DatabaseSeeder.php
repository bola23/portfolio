<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with editable demo content.
     */
    public function run(): void
    {
        $this->call([
            SiteSettingsSeeder::class,
            SkillSeeder::class,
            ProjectSeeder::class,
            ExperienceSeeder::class,
            ServiceSeeder::class,
            CredentialSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
