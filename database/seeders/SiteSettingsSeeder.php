<?php

namespace Database\Seeders;

use App\Enums\AvailabilityStatus;
use App\Enums\SocialPlatform;
use App\Models\Profile;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;

/**
 * Seeds the profile, website settings and social links.
 * Placeholder contact details must be replaced from /admin.
 */
class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'site_name' => 'Poula Adel',
            'tagline' => 'Backend-focused Laravel engineering',
            'primary_color' => '#4F46E5',
            'accent_color' => '#14B8A6',
            'default_theme' => 'light',
            'meta_title' => 'Poula Adel — Senior Full Stack Laravel Developer',
            'meta_description' => 'Portfolio of Poula Adel, a Senior Full Stack Laravel Developer from Egypt focused on backend development, REST APIs, integrations and database design.',
            'footer_text' => 'Designed & engineered with Laravel.',
            'contact_notification_email' => null,
            'show_contact_form' => true,
        ]);

        Profile::query()->updateOrCreate(['id' => 1], [
            'name' => 'Poula Adel',
            'headline' => 'Senior Full Stack Laravel Developer',
            'specialization' => 'Backend Development',
            'short_intro' => 'I design and build reliable backend systems with PHP and Laravel — REST APIs, third-party integrations, admin dashboards and well-structured databases.',
            'bio' => '<p>I am a Senior Full Stack Laravel Developer based in Egypt with around five years of experience working with PHP and Laravel. My focus is the backend: modelling data, designing APIs and connecting applications to the services they depend on.</p><p>I have worked on e-commerce systems, e-learning platforms, delivery management systems, hospital management systems and the admin dashboards that run them.</p><p><em>Demo biography — edit it from the admin dashboard.</em></p>',
            'approach' => '<p>I start from the data model and the business rules, keep controllers thin, and push complexity into well-named, testable units. I prefer framework conventions over clever abstractions, and I treat performance, security and maintainability as features rather than afterthoughts.</p>',
            'expertise_areas' => [
                'Backend development',
                'REST API development & integrations',
                'E-commerce systems',
                'E-learning platforms',
                'Delivery management systems',
                'Hospital management systems',
                'Admin dashboards',
                'Database design & optimization',
                'Third-party integrations',
            ],
            'years_of_experience' => 5,
            'availability_status' => AvailabilityStatus::Open,
            'availability_note' => 'Remote-friendly · Egypt (GMT+2)',
            'email' => 'hello@example.com',
            'phone' => null,
            'show_phone' => false,
            'location' => 'Egypt',
        ]);

        $links = [
            [SocialPlatform::GitHub, 'https://github.com/your-username'],
            [SocialPlatform::LinkedIn, 'https://www.linkedin.com/in/your-profile'],
            [SocialPlatform::Email, 'hello@example.com'],
        ];

        foreach ($links as $order => [$platform, $url]) {
            SocialLink::query()->updateOrCreate(['platform' => $platform], [
                'url' => $url,
                'sort_order' => $order,
                'is_visible' => true,
                'show_in_hero' => true,
            ]);
        }
    }
}
