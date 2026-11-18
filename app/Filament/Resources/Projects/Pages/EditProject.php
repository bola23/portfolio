<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Support\PortfolioCache;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label(fn (Project $record): string => $record->is_published ? 'Open on website' : 'Preview draft')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (Project $record): string => route('projects.show', $record), shouldOpenInNewTab: true),
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Relationship changes (technologies, gallery) are saved after the model,
     * so flush the public cache once everything is persisted.
     */
    protected function afterSave(): void
    {
        PortfolioCache::flush();
    }
}
