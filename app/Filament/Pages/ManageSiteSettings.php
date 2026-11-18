<?php

namespace App\Filament\Pages;

use App\Filament\Forms\Fields;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSiteSettings extends SingletonSettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Website settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Website settings';

    protected static ?string $slug = 'settings';

    private ?SiteSetting $settings = null;

    protected function record(): SiteSetting
    {
        return $this->settings ??= SiteSetting::query()->firstOrCreate([], ['site_name' => config('app.name')]);
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Branding')
                ->columns(2)
                ->schema([
                    TextInput::make('site_name')->required()->maxLength(255),
                    TextInput::make('tagline')->maxLength(255),
                    Fields::brandAsset('logo', 'branding')->label('Logo')->helperText('PNG/WebP. Shown in the navigation instead of the text logo.'),
                    Fields::brandAsset('favicon', 'branding', 256)->label('Favicon')->helperText('Square PNG or ICO, 256 KB max.'),
                    ColorPicker::make('primary_color')->required()->regex('/^#[0-9a-fA-F]{6}$/')
                        ->helperText('Main accent used for buttons, highlights and the 3D scene.'),
                    ColorPicker::make('accent_color')->required()->regex('/^#[0-9a-fA-F]{6}$/')
                        ->helperText('Secondary color for code and data details.'),
                ]),
            Section::make('SEO')
                ->schema([
                    TextInput::make('meta_title')->label('Default page title')->maxLength(70),
                    Textarea::make('meta_description')->label('Default meta description')->maxLength(300)->rows(2),
                    FileUpload::make('og_image')
                        ->label('Social sharing image')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png'])
                        ->maxSize(2048)
                        ->disk('public')
                        ->directory('seo')
                        ->visibility('public')
                        ->helperText('1200×630 JPG or PNG recommended.'),
                ]),
            Section::make('Contact & footer')
                ->columns(2)
                ->schema([
                    TextInput::make('contact_notification_email')
                        ->label('Notify this address about new messages')
                        ->email()
                        ->maxLength(255)
                        ->helperText('Leave empty to only store messages in the inbox.'),
                    Toggle::make('show_contact_form')->label('Show contact form')->inline(false),
                    Textarea::make('footer_text')->maxLength(500)->rows(2)->columnSpanFull(),
                ]),
            Section::make('Appearance')
                ->schema([
                    Select::make('default_theme')
                        ->label('Default color theme')
                        ->options([
                            'light' => 'Light',
                            'dark' => 'Dark',
                            'system' => "Follow the visitor's device setting",
                        ])
                        ->required()
                        ->helperText('Visitors can still switch between light and dark with the toggle in the navigation.'),
                ]),
        ];
    }
}
