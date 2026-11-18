<?php

namespace App\Filament\Resources\ContactMessages;

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

/**
 * Inbox actions shared by the messages table and the message view page.
 */
final class ContactMessageActions
{
    public static function markRead(): Action
    {
        return Action::make('markRead')
            ->label('Mark as read')
            ->icon('heroicon-o-envelope-open')
            ->visible(fn (ContactMessage $record): bool => $record->isUnread())
            ->authorize('update')
            ->action(fn (ContactMessage $record) => $record->markAsRead());
    }

    public static function markUnread(): Action
    {
        return Action::make('markUnread')
            ->label('Mark as unread')
            ->icon('heroicon-o-envelope')
            ->visible(fn (ContactMessage $record): bool => ! $record->isUnread())
            ->authorize('update')
            ->action(fn (ContactMessage $record) => $record->markAsUnread());
    }

    public static function changeStatus(): Action
    {
        return Action::make('changeStatus')
            ->label('Change status')
            ->icon('heroicon-o-tag')
            ->authorize('update')
            ->fillForm(fn (ContactMessage $record): array => ['status' => $record->status])
            ->schema([
                Select::make('status')->options(MessageStatus::class)->required(),
            ])
            ->action(function (ContactMessage $record, array $data): void {
                $status = MessageStatus::from($data['status'] instanceof MessageStatus ? $data['status']->value : $data['status']);

                $record->forceFill([
                    'status' => $status,
                    'read_at' => $status === MessageStatus::New ? null : ($record->read_at ?? now()),
                ])->save();
            });
    }

    public static function reply(): Action
    {
        return Action::make('reply')
            ->label('Reply by email')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('primary')
            ->url(fn (ContactMessage $record): string => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: '.($record->subject ?: 'Your message')))
            ->openUrlInNewTab();
    }
}
