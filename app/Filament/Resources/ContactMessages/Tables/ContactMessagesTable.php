<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Enums\MessageStatus;
use App\Filament\Resources\ContactMessages\ContactMessageActions;
use App\Models\ContactMessage;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(fn (ContactMessage $record): FontWeight => $record->isUnread() ? FontWeight::Bold : FontWeight::Normal)
                    ->description(fn (ContactMessage $record): string => $record->email),
                TextColumn::make('subject')
                    ->searchable()
                    ->placeholder('(no subject)')
                    ->description(fn (ContactMessage $record): string => str($record->message)->limit(80)->toString())
                    ->wrap(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->label('Received')->since()->sortable()
                    ->tooltip(fn (ContactMessage $record): string => $record->created_at->toDayDateTimeString()),
            ])
            ->filters([
                TernaryFilter::make('read_at')->label('Read')->nullable(),
                SelectFilter::make('status')->options(MessageStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    ContactMessageActions::markRead(),
                    ContactMessageActions::markUnread(),
                    ContactMessageActions::changeStatus(),
                    ContactMessageActions::reply(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Mark as read')
                        ->icon('heroicon-o-envelope-open')
                        ->action(fn (Collection $records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('markUnread')
                        ->label('Mark as unread')
                        ->icon('heroicon-o-envelope')
                        ->action(fn (Collection $records) => $records->each->markAsUnread())
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
