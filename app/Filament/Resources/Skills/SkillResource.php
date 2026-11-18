<?php

namespace App\Filament\Resources\Skills;

use App\Filament\Forms\Fields;
use App\Filament\Resources\Skills\Pages\ManageSkills;
use App\Models\Skill;
use App\Support\Icon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use UnitEnum;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Resume';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                Select::make('skill_category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn ($query) => $query->orderBy('sort_order'))
                    ->preload()
                    ->required(),
                Fieldset::make('Icon')
                    ->columnSpanFull()
                    ->schema([
                        Fields::icon(),
                        Fields::image('icon_path', 'skills', 256, 512)
                            ->label('…or upload an icon')
                            ->imageEditor(false),
                        ColorPicker::make('color')
                            ->label('Brand color')
                            ->regex('/^#[0-9a-fA-F]{6}$/')
                            ->helperText('Colors the icon and card accent. Leave empty to use the site colors.'),
                    ]),
                TextInput::make('proficiency')
                    ->label('Proficiency (optional)')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->helperText('Leave empty to hide the proficiency bar.'),
                Fields::sortOrder(),
                Toggle::make('is_visible')->label('Visible on website')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->defaultGroup(Group::make('category.name')->label('Category')->collapsible())
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('icon')
                    ->label('')
                    ->state(fn (Skill $record): HtmlString => new HtmlString('<span style="display:inline-flex;width:20px;height:20px">'.(
                        $record->icon_path
                            ? '<img src="'.e(Storage::disk('public')->url($record->icon_path)).'" alt="" style="width:20px;height:20px;object-fit:contain">'
                            : (Icon::svg($record->icon) ?? '')
                    ).'</span>'))
                    ->html(),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('proficiency')->suffix('%')->placeholder('—')->sortable(),
                ToggleColumn::make('is_visible')->label('Visible'),
            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name')->preload(),
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
            'index' => ManageSkills::route('/'),
        ];
    }
}
