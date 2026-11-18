<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectType;
use App\Filament\Forms\Fields;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Project')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        self::basicsTab(),
                        self::caseStudyTab(),
                        self::mediaTab(),
                        self::publishingTab(),
                    ]),
            ]);
    }

    private static function basicsTab(): Tab
    {
        return Tab::make('Basics')
            ->icon('heroicon-o-identification')
            ->schema([
                Grid::make(2)->schema([
                    Fields::titleWithSlug(),
                    Fields::slug(),
                    Textarea::make('excerpt')
                        ->label('Short description')
                        ->required()
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull(),
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name', fn ($query) => $query->orderBy('sort_order'))
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->required()->maxLength(255),
                            TextInput::make('slug')->required()->alphaDash()->unique('categories', 'slug'),
                        ])
                        ->createOptionUsing(fn (array $data): int => Category::query()->create([
                            'name' => $data['name'],
                            'slug' => Str::slug($data['slug']),
                        ])->getKey()),
                    Select::make('type')
                        ->label('Project type')
                        ->options(ProjectType::class)
                        ->default(ProjectType::WebApp)
                        ->required(),
                    TextInput::make('role')
                        ->label('My role')
                        ->placeholder('e.g. Backend Lead')
                        ->maxLength(255),
                    TextInput::make('region')
                        ->label('Region / market')
                        ->placeholder('e.g. Saudi Arabia')
                        ->maxLength(255),
                    Select::make('technologies')
                        ->relationship('technologies', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull(),
                    TextInput::make('live_url')
                        ->label('Live URL')
                        ->url()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-globe-alt'),
                    TextInput::make('repo_url')
                        ->label('Repository URL')
                        ->url()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-code-bracket'),
                ]),
            ]);
    }

    private static function caseStudyTab(): Tab
    {
        return Tab::make('Case study')
            ->icon('heroicon-o-document-text')
            ->schema([
                Fields::richText('overview')->label('Project overview'),
                Fields::richText('problem')->label('Problem & challenge'),
                Fields::richText('solution')->label('Solution'),
                Fields::richText('responsibilities')->label('My responsibilities'),
                Repeater::make('features')
                    ->label('Main features')
                    ->simple(TextInput::make('feature')->required()->maxLength(255))
                    ->reorderable()
                    ->addActionLabel('Add feature')
                    ->defaultItems(0)
                    ->columnSpanFull(),
                Fields::richText('architecture')->label('Architecture & technical details'),
            ]);
    }

    private static function mediaTab(): Tab
    {
        return Tab::make('Media')
            ->icon('heroicon-o-photo')
            ->schema([
                Fields::image('thumbnail', 'projects')
                    ->label('Thumbnail / cover image')
                    ->imageAspectRatio('16:10'),
                Repeater::make('images')
                    ->label('Gallery')
                    ->relationship()
                    ->orderColumn('sort_order')
                    ->schema([
                        Fields::image('path', 'projects/gallery')->label('Screenshot')->required(),
                        TextInput::make('caption')->maxLength(255),
                    ])
                    ->grid(2)
                    ->collapsible()
                    ->defaultItems(0)
                    ->addActionLabel('Add screenshot')
                    ->columnSpanFull(),
            ]);
    }

    private static function publishingTab(): Tab
    {
        return Tab::make('Publishing & SEO')
            ->icon('heroicon-o-megaphone')
            ->schema([
                Section::make('Visibility')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Drafts are never visible on the public website.'),
                        Toggle::make('is_featured')
                            ->label('Featured on the home page'),
                        DateTimePicker::make('published_at')
                            ->label('Publish date')
                            ->helperText('Set automatically when first published.'),
                        Fields::sortOrder(),
                    ]),
                Section::make('Search engines')
                    ->description('Optional. Falls back to the project title and short description.')
                    ->schema([
                        TextInput::make('meta_title')->maxLength(70),
                        Textarea::make('meta_description')->maxLength(300)->rows(2),
                    ]),
            ]);
    }
}
