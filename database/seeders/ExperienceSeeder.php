<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds clearly-labelled DEMO experience records. Replace them from /admin.
 */
class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            [
                'company' => 'Demo Company A',
                'position' => 'Senior Backend Developer',
                'employment_type' => 'Full-time',
                'work_mode' => 'Remote',
                'start_date' => '2025-01-01',
                'is_current' => true,
                'description' => 'Demo role — replace with your real experience from the admin dashboard.',
                'responsibilities' => ['Design and build RESTful APIs with Laravel', 'Model and optimize relational databases', 'Integrate third-party services'],
                'technologies' => ['PHP', 'Laravel', 'MySQL', 'REST API'],
            ],
            [
                'company' => 'Demo Company B',
                'position' => 'Full Stack Laravel Developer',
                'employment_type' => 'Full-time',
                'work_mode' => 'Remote',
                'start_date' => '2023-01-01',
                'end_date' => '2024-12-31',
                'description' => 'Demo role — replace with your real experience from the admin dashboard.',
                'responsibilities' => ['Build Laravel web applications end to end', 'Implement admin dashboards', 'Collaborate with stakeholders on requirements'],
                'technologies' => ['Laravel', 'JavaScript', 'Tailwind CSS', 'MySQL'],
            ],
            [
                'company' => 'Demo Company C',
                'position' => 'Backend Developer',
                'employment_type' => 'Full-time',
                'work_mode' => 'On-site',
                'start_date' => '2021-06-01',
                'end_date' => '2022-12-31',
                'description' => 'Demo role — replace with your real experience from the admin dashboard.',
                'responsibilities' => ['Develop backend features with PHP and Laravel', 'Write and optimize SQL queries'],
                'technologies' => ['PHP', 'Laravel', 'MySQL'],
            ],
        ];

        foreach ($records as $order => $record) {
            $technologies = $record['technologies'];
            unset($record['technologies']);

            $experience = Experience::query()->updateOrCreate(
                ['company' => $record['company'], 'position' => $record['position']],
                [...$record, 'sort_order' => $order, 'is_visible' => true],
            );

            $experience->technologies()->sync(
                Technology::query()->whereIn('slug', array_map(Str::slug(...), $technologies))->pluck('id'),
            );
        }
    }
}
