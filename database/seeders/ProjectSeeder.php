<?php

namespace Database\Seeders;

use App\Enums\ProjectType;
use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds categories, technologies and clearly-labelled DEMO projects.
 * Replace the demo projects with real case studies from /admin.
 */
class ProjectSeeder extends Seeder
{
    /**
     * @var list<array{0: string, 1: string|null, 2: string|null}> [name, icon, brand color]
     */
    private const TECHNOLOGIES = [
        ['PHP', 'php', '#777BB4'], ['Laravel', 'laravel', '#FF2D20'], ['MySQL', 'mysql', '#4479A1'],
        ['SQL Server', 'ui-database', '#CC2927'], ['Redis', 'redis', '#FF4438'], ['REST API', 'ui-api', null],
        ['Livewire', 'livewire', '#FB70A9'], ['Filament', 'filament', '#FDAE4B'], ['Alpine.js', 'alpinedotjs', '#8BC0D0'],
        ['Tailwind CSS', 'tailwindcss', '#06B6D4'], ['JavaScript', 'javascript', '#F7DF1E'], ['TypeScript', 'typescript', '#3178C6'],
        ['Angular', 'angular', '#DD0031'], ['Firebase', 'firebase', '#FFCA28'], ['Docker', 'docker', '#2496ED'],
        ['Nginx', 'nginx', '#009639'], ['Queues', 'ui-server', null], ['Payment Gateway', 'ui-shield', null],
    ];

    /**
     * @var list<string>
     */
    private const CATEGORIES = ['E-commerce', 'E-learning', 'Delivery & Logistics', 'Healthcare', 'Booking & Mobility'];

    public function run(): void
    {
        foreach (self::TECHNOLOGIES as $order => [$name, $icon, $color]) {
            Technology::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'icon' => $icon, 'color' => $color, 'sort_order' => $order,
            ]);
        }

        foreach (self::CATEGORIES as $order => $name) {
            Category::query()->updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $order]);
        }

        foreach ($this->projects() as $order => $data) {
            $technologies = $data['technologies'];
            $category = $data['category'];
            unset($data['technologies'], $data['category']);

            $project = Project::query()->updateOrCreate(['slug' => Str::slug($data['title'])], [
                ...$data,
                'category_id' => Category::query()->where('slug', Str::slug($category))->value('id'),
                'sort_order' => $order,
                'published_at' => ($data['is_published'] ?? true) ? now()->subDays($order) : null,
            ]);

            $project->technologies()->sync(
                Technology::query()->whereIn('slug', array_map(Str::slug(...), $technologies))->pluck('id'),
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function projects(): array
    {
        $demoNote = '<p><em>This is demo content. Replace it with a real case study from the admin dashboard.</em></p>';

        return [
            [
                'title' => 'Demo — Delivery Management Platform',
                'category' => 'Delivery & Logistics',
                'type' => ProjectType::WebAndMobile,
                'role' => 'Backend Lead',
                'excerpt' => 'Placeholder case study for a delivery platform connecting customers, drivers and administrators through a Laravel API.',
                'overview' => '<p>A sample project showing how a delivery management case study is presented: order lifecycle, driver workflows and an operations dashboard.</p>'.$demoNote,
                'problem' => '<p>Describe the business problem here — for example, manual order dispatching that did not scale with demand.</p>',
                'solution' => '<p>Describe the solution here — for example, an API-driven backend with automated dispatching and real-time status updates.</p>',
                'responsibilities' => '<ul><li>Backend architecture and API design</li><li>Third-party delivery and payment integrations</li><li>Admin dashboard features</li></ul>',
                'architecture' => '<p>Describe the architecture: modules, queues, integrations and data flow.</p>',
                'features' => ['Order lifecycle management', 'Driver dispatching workflow', 'Payment gateway integration', 'Real-time notifications', 'Operations dashboard'],
                'technologies' => ['PHP', 'Laravel', 'MySQL', 'REST API', 'Firebase', 'Queues', 'Payment Gateway'],
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Demo — Learning Management System',
                'category' => 'E-learning',
                'type' => ProjectType::WebApp,
                'role' => 'Full Stack Developer',
                'excerpt' => 'Placeholder case study for a Laravel LMS with course enrollment, examinations, certificates and role-based dashboards.',
                'overview' => '<p>A sample e-learning case study: courses, enrollments, exams and certificates with dedicated dashboards per role.</p>'.$demoNote,
                'problem' => '<p>Describe the problem the platform solved.</p>',
                'solution' => '<p>Describe how the platform solved it.</p>',
                'responsibilities' => '<ul><li>Data modelling for courses, exams and certificates</li><li>Role-based access control</li><li>Meeting scheduling integration</li></ul>',
                'features' => ['Course registration', 'Online examinations', 'Certificate generation', 'Role-based dashboards'],
                'technologies' => ['PHP', 'Laravel', 'MySQL', 'Livewire', 'Tailwind CSS'],
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Demo — E-commerce Store & Back Office',
                'category' => 'E-commerce',
                'type' => ProjectType::WebApp,
                'role' => 'Backend Developer',
                'excerpt' => 'Placeholder case study for an e-commerce website with a back office for products, orders and fulfilment.',
                'overview' => '<p>A sample e-commerce case study: catalogue, cart, checkout and an admin back office.</p>'.$demoNote,
                'features' => ['Product catalogue', 'Order management', 'Payment integration', 'Back-office dashboard'],
                'technologies' => ['PHP', 'Laravel', 'MySQL', 'Filament', 'Alpine.js', 'Payment Gateway'],
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Demo — Hospital Management System',
                'category' => 'Healthcare',
                'type' => ProjectType::Dashboard,
                'role' => 'Backend Developer',
                'excerpt' => 'Placeholder case study for a hospital management system covering patients, appointments and departments.',
                'overview' => '<p>A sample healthcare case study: patient records, appointment scheduling and departmental dashboards.</p>'.$demoNote,
                'features' => ['Patient records', 'Appointment scheduling', 'Department dashboards', 'Role-based permissions'],
                'technologies' => ['PHP', 'Laravel', 'MySQL', 'REST API'],
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Demo — Booking & Bidding API',
                'category' => 'Booking & Mobility',
                'type' => ProjectType::Api,
                'role' => 'Backend Developer',
                'excerpt' => 'Placeholder case study for a booking API with provider quotations and real-time bidding for mobile apps.',
                'overview' => '<p>A sample API case study: service requests, quotations and booking management consumed by mobile apps.</p>'.$demoNote,
                'features' => ['Service requests', 'Provider quotations', 'Real-time bidding', 'Booking management'],
                'technologies' => ['PHP', 'Laravel', 'MySQL', 'REST API', 'Redis', 'Queues'],
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'title' => 'Demo — Unpublished Draft Project',
                'category' => 'Booking & Mobility',
                'type' => ProjectType::Other,
                'excerpt' => 'This draft is not visible on the public website until it is published from the dashboard.',
                'technologies' => ['Laravel'],
                'is_featured' => false,
                'is_published' => false,
            ],
        ];
    }
}
