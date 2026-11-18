<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestMessages extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest messages';

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactMessage::query()->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->weight(fn (ContactMessage $record): FontWeight => $record->isUnread() ? FontWeight::Bold : FontWeight::Normal)
                    ->description(fn (ContactMessage $record): string => $record->email),
                TextColumn::make('subject')->placeholder('(no subject)')->limit(60),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->label('Received')->since(),
            ])
            ->recordUrl(fn (ContactMessage $record): string => ContactMessageResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Action::make('inbox')->label('Open inbox')->url(ContactMessageResource::getUrl())->link(),
            ])
            ->emptyStateHeading('No messages yet');
    }
}
