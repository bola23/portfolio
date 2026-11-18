<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label(fn (Project $record): string => $record->is_published ? 'Open on website' : 'Preview draft')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (Project $record): string => route('projects.show', $record), shouldOpenInNewTab: true)
                ->hidden(fn (Project $record): bool => $record->trashed()),
            EditAction::make(),
        ];
    }
}
