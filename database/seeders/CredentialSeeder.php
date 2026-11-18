<?php

namespace Database\Seeders;

use App\Enums\CredentialType;
use App\Models\Credential;
use Illuminate\Database\Seeder;

/**
 * Seeds clearly-labelled DEMO education and certification records.
 */
class CredentialSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = [
            [CredentialType::Education, 'Demo — B.Sc. in Computer Science', 'Demo University', 2019, 2023],
            [CredentialType::Certification, 'Demo — Full Stack Web Development', 'Demo Academy', null, 2021],
            [CredentialType::Certification, 'Demo — Python Developer Program', 'Demo Learning Center', null, 2020],
        ];

        foreach ($credentials as $order => [$type, $title, $issuer, $start, $end]) {
            Credential::query()->updateOrCreate(['title' => $title], [
                'type' => $type,
                'issuer' => $issuer,
                'start_year' => $start,
                'end_year' => $end,
                'sort_order' => $order,
                'is_visible' => true,
            ]);
        }
    }
}
