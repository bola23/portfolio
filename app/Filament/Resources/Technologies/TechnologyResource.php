<?php

namespace App\Filament\Resources\Technologies;

use App\Filament\Forms\Fields;
use App\Filament\Resources\Technologies\Pages\ManageTechnologies;
use App\Models\Technology;
use App\Support\Icon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static string|UnitEnum|null $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fields::titleWithSlug('name'),
                Fields::slug(),
                Fields::icon(),
                ColorPicker::make('color')->label('Brand color')->regex('/^#[0-9a-fA-F]{6}$/'),
                Fields::sortOrder(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('icon')
                    ->label('')
                    ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString('<span style="display:inline-flex;width:20px;height:20px">'.(Icon::svg($state) ?? '').'</span>'))
                    ->html(),
                TextColumn::make('name')->searchable()->weight('bold'),
                ColorColumn::make('color'),
                TextColumn::make('projects_count')->counts('projects')->label('Projects')->badge(),
                TextColumn::make('experiences_count')->counts('experiences')->label('Roles')->badge()->color('gray'),
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
            'index' => ManageTechnologies::route('/'),
        ];
    }
}
