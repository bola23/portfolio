<?php

namespace App\Filament\Resources\Experiences\Pages;

use App\Filament\Resources\Experiences\ExperienceResource;
use App\Support\PortfolioCache;
use Filament\Resources\Pages\CreateRecord;

class CreateExperience extends CreateRecord
{
    protected static string $resource = ExperienceResource::class;

    protected function afterCreate(): void
    {
        PortfolioCache::flush();
    }
}
