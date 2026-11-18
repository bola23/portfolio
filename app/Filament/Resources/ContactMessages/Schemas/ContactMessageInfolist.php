<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('subject')->placeholder('(no subject)')->weight('bold'),
                        TextEntry::make('message')->hiddenLabel()->prose()->extraAttributes(['style' => 'white-space: pre-line']),
                    ]),
                Section::make('Sender')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->copyable()->icon('heroicon-o-envelope'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('created_at')->label('Received')->dateTime(),
                        TextEntry::make('read_at')->dateTime()->placeholder('Unread'),
                        TextEntry::make('ip_address')->label('IP address')->placeholder('—'),
                        TextEntry::make('user_agent')->size('xs')->color('gray')->placeholder('—'),
                    ]),
            ])
            ->columns(3);
    }
}
