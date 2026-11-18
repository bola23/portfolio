<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageActions;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var ContactMessage $message */
        $message = $this->getRecord();
        $message->markAsRead();
    }

    protected function getHeaderActions(): array
    {
        return [
            ContactMessageActions::reply(),
            ContactMessageActions::markUnread(),
            ContactMessageActions::changeStatus(),
            DeleteAction::make(),
        ];
    }
}
