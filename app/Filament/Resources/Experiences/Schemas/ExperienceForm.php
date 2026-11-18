<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Filament\Forms\Fields;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('company')->required()->maxLength(255),
                        TextInput::make('position')->required()->maxLength(255),
                        TextInput::make('company_url')->label('Company website')->url()->maxLength(255),
                        TextInput::make('location')->maxLength(255)->placeholder('e.g. Cairo, Egypt'),
                        Select::make('employment_type')
                            ->options(array_combine($types = ['Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship'], $types)),
                        Select::make('work_mode')
                            ->options(array_combine($modes = ['Remote', 'Hybrid', 'On-site'], $modes)),
                    ]),
                Section::make('Period')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('start_date')->required()->native(false)->displayFormat('M Y'),
                        DatePicker::make('end_date')
                            ->native(false)
                            ->displayFormat('M Y')
                            ->afterOrEqual('start_date')
                            ->hidden(fn (Get $get): bool => (bool) $get('is_current'))
                            ->required(fn (Get $get): bool => ! $get('is_current')),
                        Toggle::make('is_current')->label('I currently work here')->live()->inline(false),
                    ]),
                Section::make('Details')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('description')->rows(3)->maxLength(2000),
                        Repeater::make('responsibilities')
                            ->simple(TextInput::make('responsibility')->required()->maxLength(500))
                            ->reorderable()
                            ->defaultItems(0)
                            ->addActionLabel('Add responsibility'),
                        Select::make('technologies')
                            ->relationship('technologies', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),
                Section::make('Display')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_visible')->label('Visible on website')->default(true),
                        Fields::sortOrder()->helperText('Lower numbers appear first; ties are ordered by date.'),
                    ]),
            ]);
    }
}
