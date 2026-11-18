<?php

namespace App\Filament\Pages;

use App\Enums\AvailabilityStatus;
use App\Filament\Forms\Fields;
use App\Models\Profile;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageProfile extends SingletonSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Profile';

    protected static ?string $slug = 'profile';

    private ?Profile $profile = null;

    protected function record(): Profile
    {
        return $this->profile ??= Profile::query()->firstOrCreate([], ['name' => config('app.name'), 'headline' => 'Developer']);
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Identity')
                ->description('Shown in the hero and about sections.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('headline')->label('Job title')->required()->maxLength(255),
                    TextInput::make('specialization')->label('Main specialization')->maxLength(255),
                    TextInput::make('location')->maxLength(255),
                    Textarea::make('short_intro')->label('Short introduction')->maxLength(600)->rows(3)->columnSpanFull(),
                    Fields::image('avatar', 'profile', 1000)->label('Profile image')->avatar()->imageEditor(),
                ]),
            Section::make('About')
                ->schema([
                    Fields::richText('bio')->label('Biography'),
                    Fields::richText('approach')->label('My approach to software development'),
                    TagsInput::make('expertise_areas')->label('Areas of experience')->placeholder('Add an area and press Enter'),
                    TextInput::make('years_of_experience')->numeric()->integer()->minValue(0)->maxValue(60)->required(),
                ]),
            Section::make('Availability')
                ->columns(2)
                ->schema([
                    Select::make('availability_status')->options(AvailabilityStatus::class)->required(),
                    TextInput::make('availability_note')->maxLength(255)->placeholder('e.g. Remote-friendly · GMT+2'),
                ]),
            Section::make('Contact & CV')
                ->columns(2)
                ->schema([
                    TextInput::make('email')->email()->maxLength(255),
                    TextInput::make('phone')->tel()->maxLength(32),
                    Toggle::make('show_phone')->label('Show phone number publicly')->columnSpanFull(),
                    FileUpload::make('cv_path')
                        ->label('CV / résumé (PDF)')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(5120)
                        ->disk('public')
                        ->directory('cv')
                        ->visibility('public')
                        ->downloadable()
                        ->helperText('PDF up to 5 MB. Served at /cv; the "Download CV" button is hidden when empty.')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
