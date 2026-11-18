<?php

namespace App\Filament\Resources\Experiences\Tables;

use App\Models\Experience;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('position')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Experience $record): string => $record->company),
                TextColumn::make('company')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('period')
                    ->state(fn (Experience $record): string => $record->period),
                TextColumn::make('work_mode')->badge()->color('gray')->toggleable(),
                TextColumn::make('technologies.name')->badge()->limitList(3)->toggleable(),
                ToggleColumn::make('is_visible')->label('Visible'),
            ])
            ->filters([
                TernaryFilter::make('is_current')->label('Current role'),
                TernaryFilter::make('is_visible')->label('Visible'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
