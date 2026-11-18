<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->columnSpanFull()->schema([
                    Section::make('Summary')
                        ->columnSpan(2)
                        ->columns(2)
                        ->schema([
                            TextEntry::make('title')->columnSpanFull()->weight('bold'),
                            TextEntry::make('excerpt')->label('Short description')->columnSpanFull(),
                            TextEntry::make('category.name')->label('Category')->badge()->placeholder('—'),
                            TextEntry::make('type')->badge(),
                            TextEntry::make('role')->placeholder('—'),
                            TextEntry::make('region')->placeholder('—'),
                            TextEntry::make('technologies.name')->label('Technologies')->badge()->columnSpanFull()->placeholder('—'),
                            TextEntry::make('features')->label('Main features')->listWithLineBreaks()->bulleted()->columnSpanFull()->placeholder('—'),
                        ]),
                    Section::make('Status')
                        ->columnSpan(1)
                        ->schema([
                            ImageEntry::make('thumbnail')->disk('public')->imageHeight(140)->placeholder('No thumbnail'),
                            IconEntry::make('is_published')->label('Published')->boolean(),
                            IconEntry::make('is_featured')->label('Featured')->boolean(),
                            TextEntry::make('published_at')->dateTime()->placeholder('—'),
                            TextEntry::make('slug')->prefix('/projects/')->copyable(),
                            TextEntry::make('live_url')->label('Live URL')->url(fn (Project $record): ?string => $record->live_url, true)->placeholder('—'),
                            TextEntry::make('repo_url')->label('Repository')->url(fn (Project $record): ?string => $record->repo_url, true)->placeholder('—'),
                            TextEntry::make('deleted_at')->dateTime()->color('danger')->visible(fn (Project $record): bool => $record->trashed()),
                        ]),
                ]),
                Section::make('Case study')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('overview')->html()->placeholder('—'),
                        TextEntry::make('problem')->label('Problem & challenge')->html()->placeholder('—'),
                        TextEntry::make('solution')->html()->placeholder('—'),
                        TextEntry::make('responsibilities')->html()->placeholder('—'),
                        TextEntry::make('architecture')->html()->placeholder('—'),
                    ]),
                Section::make('Gallery')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        RepeatableEntry::make('images')
                            ->hiddenLabel()
                            ->grid(3)
                            ->schema([
                                ImageEntry::make('path')->hiddenLabel()->disk('public')->imageHeight(140),
                                TextEntry::make('caption')->hiddenLabel()->placeholder(''),
                            ])
                            ->placeholder('No screenshots yet.'),
                    ]),
            ]);
    }
}
