<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectType;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->disk('public')
                    ->label('')
                    ->imageWidth(64)
                    ->imageHeight(40),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Project $record): string => '/projects/'.$record->slug),
                TextColumn::make('category.name')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('technologies_count')
                    ->label('Tech')
                    ->counts('technologies')
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_published')
                    ->label('Published'),
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
                TextColumn::make('published_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
                TernaryFilter::make('is_featured')->label('Featured'),
                SelectFilter::make('category')->relationship('category', 'name')->preload(),
                SelectFilter::make('type')->options(ProjectType::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('openPublic')
                        ->label('Open on website')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Project $record): string => route('projects.show', $record), shouldOpenInNewTab: true)
                        ->hidden(fn (Project $record): bool => $record->trashed()),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->icon('heroicon-o-eye')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('unpublish')
                        ->icon('heroicon-o-eye-slash')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
