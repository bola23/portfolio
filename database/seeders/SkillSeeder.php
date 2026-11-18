<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Skill categories with [name, icon key, brand color] entries. A null color
     * uses the site palette. Proficiency is left empty on purpose — set it from
     * the dashboard if you want to show it.
     *
     * @var array<string, list<array{0: string, 1: string|null, 2: string|null}>>
     */
    private const SKILLS = [
        'Backend Development' => [
            ['PHP', 'php', '#777BB4'], ['Laravel', 'laravel', '#FF2D20'], ['Eloquent ORM', 'laravel', '#F05340'],
            ['RESTful APIs', 'ui-api', null], ['OOP & SOLID', 'ui-layers', null], ['Queues & Background Jobs', 'ui-server', null],
            ['Authentication & Authorization', 'ui-shield', null],
        ],
        'Frontend Development' => [
            ['JavaScript', 'javascript', '#E8C400'], ['TypeScript', 'typescript', '#3178C6'], ['Angular', 'angular', '#DD0031'],
            ['HTML5', 'html5', '#E34F26'], ['CSS', 'css', '#663399'], ['Tailwind CSS', 'tailwindcss', '#06B6D4'],
            ['Bootstrap', 'bootstrap', '#7952B3'], ['Alpine.js', 'alpinedotjs', '#77C1D2'],
        ],
        'Databases' => [
            ['MySQL', 'mysql', '#4479A1'], ['SQL Server', 'ui-database', '#CC2927'], ['Database Design', 'ui-database', null],
            ['Query Optimization', 'ui-gauge', null],
        ],
        'DevOps & Tools' => [
            ['Git', 'git', '#F05032'], ['GitHub', 'github', null], ['Docker', 'docker', '#2496ED'], ['Linux', 'linux', '#E8B500'],
            ['Nginx', 'nginx', '#009639'], ['Jenkins', 'jenkins', '#D24939'], ['Postman', 'postman', '#FF6C37'],
        ],
        'Integrations' => [
            ['Payment Gateways', 'ui-shield', null], ['Firebase / FCM', 'firebase', '#FFA000'], ['Third-party APIs', 'ui-plug', null],
            ['Zoom API', 'zoom', '#0B5CFF'],
        ],
    ];

    public function run(): void
    {
        $categoryOrder = 0;

        foreach (self::SKILLS as $categoryName => $skills) {
            $category = SkillCategory::query()->updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'sort_order' => $categoryOrder++],
            );

            foreach ($skills as $order => [$name, $icon, $color]) {
                Skill::query()->updateOrCreate(
                    ['skill_category_id' => $category->id, 'name' => $name],
                    ['icon' => $icon, 'color' => $color, 'sort_order' => $order, 'is_visible' => true],
                );
            }
        }
    }
}
