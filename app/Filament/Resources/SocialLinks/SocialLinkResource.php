<?php

namespace App\Filament\Resources\SocialLinks;

use App\Enums\SocialPlatform;
use App\Filament\Forms\Fields;
use App\Filament\Resources\SocialLinks\Pages\ManageSocialLinks;
use App\Models\SocialLink;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Social links';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'url';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('platform')->options(SocialPlatform::class)->required()->live(),
                TextInput::make('label')->maxLength(255)->placeholder('Defaults to the platform name'),
                TextInput::make('url')
                    ->label(fn (Get $get): string => $get('platform') === SocialPlatform::Email->value || $get('platform') === SocialPlatform::Email ? 'Email address' : 'URL')
                    ->required()
                    ->maxLength(255)
                    ->rules(fn (Get $get): array => in_array($get('platform'), [SocialPlatform::Email, SocialPlatform::Email->value], true) ? ['email'] : ['url:https,http'])
                    ->columnSpanFull(),
                Fields::sortOrder(),
                Toggle::make('is_visible')->label('Visible on website')->default(true),
                Toggle::make('show_in_hero')->label('Show in hero section')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('platform')->badge(),
                TextColumn::make('url')->searchable()->limit(50),
                ToggleColumn::make('is_visible')->label('Visible'),
                ToggleColumn::make('show_in_hero')->label('In hero'),
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

    public static function getPages(): array
    {
        return [
            'index' => ManageSocialLinks::route('/'),
        ];
    }
}
