<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['Backend Development', 'ui-server', 'Robust server-side applications with clean architecture, solid data models and predictable behaviour.', ['Domain modelling', 'Queues & scheduled jobs', 'Automated tests']],
            ['Laravel Application Development', 'laravel', 'Full Laravel applications from database to dashboard, following framework conventions.', ['Admin dashboards', 'Authentication & roles', 'Deployment setup']],
            ['REST API Development', 'ui-api', 'Versioned, documented APIs for web and mobile clients, with consistent responses and authentication.', ['API design', 'Token authentication', 'Postman collections']],
            ['Database Design', 'ui-database', 'Normalized schemas, indexes and queries designed around how the data is actually used.', ['Schema design', 'Indexing strategy', 'Query optimization']],
            ['Third-party API Integrations', 'ui-plug', 'Reliable integrations with payment gateways, notification services and external platforms.', ['Payment gateways', 'Webhooks', 'Push notifications']],
            ['Application Optimization', 'ui-gauge', 'Profiling and improving existing Laravel applications — queries, caching and code structure.', ['Performance audit', 'N+1 query fixes', 'Caching strategy']],
        ];

        foreach ($services as $order => [$title, $icon, $summary, $deliverables]) {
            Service::query()->updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'icon' => $icon,
                'summary' => $summary,
                'deliverables' => $deliverables,
                'sort_order' => $order,
                'is_visible' => true,
            ]);
        }
    }
}
