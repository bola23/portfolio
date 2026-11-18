<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Experiences\ExperienceResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Skills\SkillResource;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|array|null $columns = 3;

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $projects = Project::query()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published')
            ->toBase()
            ->first();

        $total = (int) $projects->total;
        $published = (int) $projects->published;
        $unread = ContactMessage::query()->unread()->count();

        return [
            Stat::make('Total projects', $total)
                ->icon(Heroicon::OutlinedRectangleStack)
                ->url(ProjectResource::getUrl()),
            Stat::make('Published projects', $published)
                ->icon(Heroicon::OutlinedEye)
                ->color('success')
                ->url(ProjectResource::getUrl(parameters: ['filters' => ['is_published' => ['value' => 1]]])),
            Stat::make('Draft projects', $total - $published)
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('warning')
                ->url(ProjectResource::getUrl(parameters: ['filters' => ['is_published' => ['value' => 0]]])),
            Stat::make('Skills', Skill::query()->count())
                ->description(Skill::query()->where('is_visible', true)->count().' visible')
                ->icon(Heroicon::OutlinedSparkles)
                ->url(SkillResource::getUrl()),
            Stat::make('Experience records', Experience::query()->count())
                ->icon(Heroicon::OutlinedBriefcase)
                ->url(ExperienceResource::getUrl()),
            Stat::make('Unread messages', $unread)
                ->icon(Heroicon::OutlinedInboxArrowDown)
                ->color($unread > 0 ? 'danger' : 'gray')
                ->url(ContactMessageResource::getUrl()),
        ];
    }
}
