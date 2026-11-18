<?php

namespace App\Filament\Resources\Credentials;

use App\Enums\CredentialType;
use App\Filament\Forms\Fields;
use App\Filament\Resources\Credentials\Pages\ManageCredentials;
use App\Models\Credential;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class CredentialResource extends Resource
{
    protected static ?string $model = Credential::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Resume';

    protected static ?string $navigationLabel = 'Education & certificates';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        $yearField = fn (string $name): TextInput => TextInput::make($name)
            ->numeric()
            ->integer()
            ->minValue(1950)
            ->maxValue((int) date('Y') + 6);

        return $schema
            ->components([
                Select::make('type')->options(CredentialType::class)->required()->default(CredentialType::Certification),
                TextInput::make('title')->required()->maxLength(255),
                TextInput::make('issuer')->label('Institution / issuer')->required()->maxLength(255),
                TextInput::make('url')->label('Credential URL')->url()->maxLength(255),
                $yearField('start_year')->label('Start year (optional)'),
                $yearField('end_year')->label('Completion year')->gte('start_year'),
                Fields::sortOrder(),
                Toggle::make('is_visible')->label('Visible on website')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')->searchable()->weight('bold')
                    ->description(fn (Credential $record): string => $record->issuer),
                TextColumn::make('type')->badge(),
                TextColumn::make('period')->state(fn (Credential $record): ?string => $record->period)->placeholder('—'),
                ToggleColumn::make('is_visible')->label('Visible'),
            ])
            ->filters([
                SelectFilter::make('type')->options(CredentialType::class),
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
            'index' => ManageCredentials::route('/'),
        ];
    }
}
